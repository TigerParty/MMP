<?php

namespace App\Services;

use App\Argo\Survey;
use Carbon\Carbon;
use DB;
use Log;
use PhpImap\IncomingMail;
use PhpImap\IncomingMailAttachment;
use PhpImap\Mailbox as ImapMailbox;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class SurveyService
{
    private $apiKey;

    public function __construct()
    {
    }

    /**
     * @param string $driver Force to specify the driver of survey service, remain null to fetch by default driver which set in config.
     *      'voto': fetch from voto
     *      'mailbox': fetch from mailbox
     *      'all': fetch both voto and mailbox
     */
    public function update($driver = null)
    {
        if (is_null($driver)) {
            $targetDriver = config('services.survey.default');
        } else {
            $targetDriver = $driver;
        }

        if ($targetDriver == 'all') {
            $this->updateVoto();
            $this->updateArgoPbx();
        } elseif ($targetDriver == 'mailbox') {
            $this->updateArgoPbx();
        } elseif ($targetDriver == 'voto') {
            $this->updateVoto();
        } else {
            throw new \Exception("Unknown survey driver:$targetDriver");
        }
    }

    public function updateVoto()
    {

        $apiKey = config('services.survey.drivers.voto.api_key');
        $surveyId = config('services.survey.drivers.voto.survey_id');

        $votoListUrl = "https://go.votomobile.org/api/v1/surveys/$surveyId/delivery_logs?api_key=$apiKey";
        $deliveryLogs = $this->fetchVotoList($votoListUrl);

        Log::info("Updating Voto Survey data...");

        if ($deliveryLogs) {
            $newDeliveryLogs = [];

            $existVotoIds = Survey::select('source_id')
                ->where('source', '=', 'voto')
                ->get()
                ->pluck('source_id')
                ->toArray();

            foreach ($deliveryLogs as $deliveryLog) {
                $deliveryLogId = object_get($deliveryLog, 'id');

                if (!in_array($deliveryLogId, $existVotoIds)) {
                    $votoLogUrl = "https://go.votomobile.org/api/v1/surveys/$surveyId/delivery_logs/$deliveryLogId?api_key=$apiKey";
                    $deliveryLogDetail = $this->fetchVotoLog($votoLogUrl);
                    $deliveryLogDetail->base = $deliveryLog;

                    Log::info("Get new delivery log: $deliveryLogId");

                    array_push($newDeliveryLogs, [
                        'source' => 'voto',
                        'source_id' => $deliveryLogId,
                        'status' => 'new',
                        'payload' => json_encode($deliveryLogDetail),
                        'created_at' => new Carbon(),
                        'updated_at' => new Carbon(),
                    ]);
                }
            }

            try {
                DB::beginTransaction();

                $existVotoIds = Survey::select('source_id')
                    ->where('source', '=', 'voto')
                    ->get()
                    ->pluck('source_id')
                    ->toArray();

                $newDeliveryLogs = array_where($newDeliveryLogs, function ($key, $value) use ($existVotoIds) {
                    return !in_array($value['source_id'], $existVotoIds);
                });

                Survey::insert($newDeliveryLogs);
                DB::commit();
            } catch (Exception $e) {
                DB::rollback();
                Log::error($e);
            }

        } else {
            Log::info("Empty Voto Survey delivery log");
        }
        Log::info("Voto Survey updated");
    }

    private function fetchVotoList($url, $result = [], $maxDeep = 0)
    {
        if ($maxDeep > 10) {
            Log::warning('Fetch Voto List over 10 page, interrupt');
            return $result;
        }

        $response = json_decode(file_get_contents($url));

        $result = array_merge($result, object_get($response, 'data.delivery_logs', []));
        $nextUrl = object_get($response, 'pagination.nextURL', null);

        if ($nextUrl) {
            $result = $this->fetchVotoList($nextUrl, $result, ++$maxDeep);
        }

        return $result;
    }

    private function fetchVotoLog($url)
    {
        return json_decode(file_get_contents($url));
    }

    public function updateArgoPbx()
    {
        Log::info("Updating ArgoPbx...");

        $mailbox = new ImapMailbox($this->getMailInboxConnentionInfo(),
            config('services.survey.drivers.mailbox.username'),
            config('services.survey.drivers.mailbox.password'),
            storage_path('/survey'));

        $mailsIds = $mailbox->searchMailbox(config('services.survey.drivers.mailbox.fetch_rule'));

        if (!$mailsIds) {
            Log::info("Empty ArgoPbx result");
            return false;
        }

        $existPbxIds = Survey::select('source_id')
            ->where('source', '=', 'argopbx')
            ->get()
            ->pluck('source_id')
            ->toArray();

        foreach ($mailsIds as $mailId) {
            try {
                if (!in_array($mailId, $existPbxIds)) {
                    $mail = $mailbox->getMail($mailId);
                    $attachmentController = new \App\Http\Controllers\AttachmentController;
                    foreach ($mail->getAttachments() as $attachment) {
                        $file = new UploadedFile($attachment->filePath, $attachment->name);
                        $fileUploading = $attachmentController->doUpload($file);
                        $fileData = json_decode($fileUploading->getContent());
                        $voiceInfo = json_decode($mail->textPlain, true);

                        $newSurvey = new Survey;
                        $newSurvey->source = 'argopbx';
                        $newSurvey->source_id = $mail->id;
                        $newSurvey->status = 'new';
                        $newSurvey->payload = array(
                            'data' => array(
                                'questions' => array(
                                    array(
                                        'title' => 'Please update the issue',
                                        'response_type' => 3,
                                        'response' => array(
                                            'open_audio_url' => asset('/file/' . $fileData->id)
                                        )
                                    )
                                )
                            ),
                            'base' => array(
                                'phone' => array_key_exists('from', $voiceInfo) ? $voiceInfo['from'] : null,
                                'start_timestamp' => array_key_exists('date', $voiceInfo) ? $voiceInfo['date'] : null,
                            )
                        );
                        $newSurvey->save();
                        \File::delete($attachment->filePath);

                        Log::info("Got new Argo PBX survey voice, id:$newSurvey->id");
                    }
                }
            } catch (\Exception $e) {
                Log::info($e);
            }
        }

        Log::info("ArgoPbx Survey updated");
        return true;
    }

    private function getMailInboxConnentionInfo()
    {
        $host = config('services.survey.drivers.mailbox.host');
        $encryption = config('services.survey.drivers.mailbox.encryption');
        $protocol = config('services.survey.drivers.mailbox.protocol');
        $connectionInfo = '{' . $host . '/' . $protocol . '/' . $encryption . '}INBOX';
        return $connectionInfo;
    }
}
