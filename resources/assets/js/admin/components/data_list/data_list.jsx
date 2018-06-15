import React from 'react'
import FieldSelect from './field/select'
import FieldApprovedBtn from './field/approved_btn'
import FieldEditBtn from './field/edit_btn'
import FieldDeleteBtn from './field/delete_btn'


class DataList extends React.Component {
    constructor(props) {
        super(props)
    }

    render() {
        const { data,
            dateTimeFromate: DATETIME_FORMATE,
            detialBaseUrl,
            regionLabels,
            statusList,
            handleEdit,
            handleDelete,
            updateStatus,
            updateApproval } = this.props
        return (
            <div className="row data-list text-greyish-brown">
                <div className="col-12">
                    <div className="table-responsive">
                        <table className="table">
                            <tbody>
                            {
                                data.map((item, index) => {
                                    return(
                                        <tr key={index} className={`font-weight-bold item ${item.is_new? 'new':''}`}>
                                            <td className="text-nowrap min-width my-3">
                                                <div className={`img-square-box ${item.cover_image ? '' : 'opacity-0-5'}`}>
                                                    <div className="content" style={{ backgroundImage: `${item.cover_image ? `url(${item.cover_image})` : 'url(/images/logo.png)'}`}}>
                                                    </div>
                                                </div>
                                            </td>
                                            <td className={`text-nowrap
                                                align-middle
                                                min-width
                                                ${!item.is_new ? 'opacity-0-8' : ''}`}>
                                                <div className="d-inline-flex flex-column font-size-16 position-relative">
                                                    <a className="text-nowrap
                                                        text-truncate
                                                        font-size-18
                                                        text-primary
                                                        item-name
                                                        mute-button"
                                                        href={`${detialBaseUrl}/${item.id}`}>
                                                        {item.title}
                                                    </a>
                                                    <div className="mb-3 text-nowrap text-truncate">
                                                        {regionLabels.map((region,index)=>{
                                                            return (
                                                                <span key={index}>{item.regions[region]}
                                                                    {`${_.size(item.regions) - 1 > index ? ', ' : ''}`}</span>
                                                            )
                                                        })}
                                                    </div>
                                                    <div className="opacity-0-5">
                                                        {_.get(item, 'updated_at')  && moment(item.updated_at).format(DATETIME_FORMATE)}
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="align-middle" align="right">
                                                <div className="d-inline-flex funtion-tools">
                                                    <div className="px-3">
                                                        <FieldSelect list={statusList}
                                                            value={!_.isNull(item.status_id) ? item.status_id:'' }
                                                            defaultOption="status"
                                                            blackTheme={true}
                                                            handleValueChange={(value)=> {updateStatus(item.id, value)}} />
                                                    </div>
                                                    <div className="px-3"><FieldApprovedBtn active={item.is_approved} handleClick={() => { updateApproval(item.id, !item.is_approved)}} /></div>
                                                    <div className="px-3"><FieldEditBtn id={item.id} handleEdit={handleEdit}/></div>
                                                    <div className="px-3"><FieldDeleteBtn id={item.id} handleDelete={handleDelete} /></div>
                                                </div>
                                            </td>

                                        </tr>
                                    )}
                                )
                            }
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        )
    }

}

export default DataList
