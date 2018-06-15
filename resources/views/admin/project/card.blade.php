<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>{{$project->title}} - {{ trans('site.title') }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600" rel="stylesheet">
  <style type="text/css">
    .standard-card {
      position: relative;
      width: 50%;
      min-width: 300px;
      height: auto;
      margin: auto;
      background-image: url("{{ asset('images/standard_card/standard_card_bg.jpg') }}");
      border-radius: 5px;
      overflow: hidden;
    }
    .other {
      text-align: center;
      margin: 1.5em 0;
    }
    .other button{
      font-size: 1.5em;
      margin: 0 0.5em;
      width: 5em;
    }

    @media print {
      .standard-card {
        width: 3.37in;
        height: 2.125in;
        -webkit-print-color-adjust: exact !important;
        color-adjust: exact !important;
      }
      .other {
        display: none;
      }
    }

    .standard-card .standard-img {
      display: block;
    }

    .standard-card .watermark-img {
      position: absolute;
      width: 80%;
      left: -10%;
      bottom: -50%;
      z-index: 1;
    }

    @media print {
      .standard-card .watermark-img {
        bottom: -55%;
      }
    }

    .standard-card .header {
      position: absolute;
      right: 0px;
      left: 0px;
      height: 20%;
      padding: .5vw .8vw;
      background: #40b3ff;
      border-top-left-radius: 5px;
      border-top-right-radius: 5px;
    }

    @media print {
      .standard-card .header {
        padding: .3rem .5rem;
      }
    }

    .standard-card .header .col-left {
      position: relative;
      width: 20%;
    }

    @media screen and (max-width: 500px) {
      .standard-card .header .col-left {
        width: 26%;
      }
    }

    @media print {
      .standard-card .header .col-left {
        width: 18%;
      }
    }

    .standard-card .header .col-left img {
      position: absolute;
      height: 90%;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
    }

    .standard-card .header .col-right {
      width: 80%;
    }

    @media print {
      .standard-card .header .col-right {
        text-align: center;
      }
    }

    .standard-card .header .col-right div {
      font-size: 2.2vw;
      color: #fff;
      font-family: "Open Sans";
    }

    @media screen and (max-width: 500px) {
      .standard-card .header .col-right div {
        padding-left: 3%;
        font-size: 2.5vw;
      }
    }

    @media screen and (max-width: 400px) {
      .standard-card .header .col-right div {
        padding-left: 3%;
        font-size: 2.5vw;
      }
    }

    @media print {
      .standard-card .header .col-right div {
        line-height: 12px;
        font-size: 0.9rem;
      }
    }

    .card-title {
      overflow: hidden;
      text-overflow: clip;
      white-space: nowrap;
      width: 40vw;
    }

    @media print {
      .card-title {
        width: 33vw;
      }
    }

    .standard-card .main {
      position: absolute;
      top: 20%;
      left: 0;
      right: 0;
      bottom: 0;
      padding: 1vw 3vw;
    }

    @media screen and (max-width: 500px) {
      .standard-card .main {
        padding: 1vw 5vw;
      }
    }

    @media print {
      .standard-card .main {
        padding: 1rem .8rem .5rem .8rem;
      }
    }

    .standard-card .main .col-left {
      position: relative;
    }

    .standard-card .main .col-left table {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      z-index: 2;
    }
    @media print {
      .standard-card .main .col-left table {
        top: 55%;
      }
    }

    .standard-card .main .col-left table caption {
      font-family: "Open Sans";
      font-size: 1.5vw;
      padding-bottom: 1.5vw;
      color: #414141;
      width: 100%;
    }

    @media print {
      .standard-card .main .col-left table caption {
        font-size: 0.7rem;
        padding-bottom: 0.3rem;
      }
    }

    .standard-card .main .col-left table tbody tr td {
      font-family: "Open Sans";
      color: #414141;
      font-size: 1.5vw;
      padding-bottom: .5vw;
    }

    @media print {
      .standard-card .main .col-left table tbody tr td {
        font-size: .8rem;
        padding-bottom: .1rem;
      }
    }

    .standard-card .main .col-right {
      width: 44%;
      position: relative;
    }

    .standard-card .main .col-right img {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
    }

    @media print {
      .standard-card .main .col-right {
        width: 30%;
        position: relative;
      }
    }

    /* helper style*/
    .content {
      display: table;
      width: 100%;
      height: 100%;
    }

    .content .col {
      display: table-cell;
      height: 100%;
      vertical-align: middle;
    }

    .text-align-left {
      text-align: left;
    }

    .thin-font-weight {
      font-weight: 300;
    }

    .normal-font-weight {
      font-weight: 400;
      padding-left: 5px;
    }

    .bold-font-weight {
      font-weight: 600;
    }

    .transform-uppercase {
      text-transform: uppercase;
    }

    .img-responsive {
      width: 100%;
      height: auto;
    }
  </style>
</head>

<body>
  <div class="standard-card">
    <img src="{{ asset('legacy/images/standard_card/standard_card_watermark.png') }}" class="watermark-img">
    <div class="header">
      <div class="content">
        <div class="col col-left">
          @if ($parentProject && $parentProject->default_img_id)
            <img src="{{ asset("file/$parentProject->default_img_id") }}">
          @else
            <img src="{{ asset(config('argodf.theme.logo')) }}">
          @endif
        </div>
        <div class="col col-right">
          @if ($parentProject && $parentProject->title)
            <div class="bold-font-weight transform-uppercase card-title">{{ $parentProject->title }}</div>
          @else
            <div class="bold-font-weight transform-uppercase card-title">{{ $project->container->name }} Identity</div>
          @endif
        </div>
      </div>
    </div>
    <div class="main">
      <div class="content">
        <div class="col col-left">
          <table>
            <caption class="text-align-left bold-font-weight transform-uppercase">{{ $project->title }}</caption>
            @foreach ($projectValue as $field)
              @if ($field->name)
                <tr>
                  <td class="thin-font-weight">{{ $field->name }}</td>
                  <td class="normal-font-weight">{{ $field->value }}</td>
                </tr>
              @endif
            @endforeach
          </table>
        </div>
        <div class="col col-right">
          @if ($project->cover_image_id)
            <img class="img-responsive" src="{{ asset("file/$project->cover_image_id") }}" onerror="this.style.height='80%';this.src='{{ asset('images/standard_card/standard_card.png') }}'">
          @else
            <img class="img-responsive" src="{{ asset('images/standard_card/standard_card.png') }}" style="height: 80%;">
          @endif
        </div>
      </div>
    </div>

    <img src="{{ asset('images/standard_card/standard_card.png') }}" class="standard-img img-responsive">
  </div>
  <div class="other">
    <button onclick="window.print()">Print</button>
    <button onclick="window.close()">Close</button>
  </div>
</body>
</html>
