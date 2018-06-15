import React from 'react'
import FieldSelect from './field/select'


class DataListMobile extends React.Component {
    constructor(props) {
        super(props)
    }

    render() {
        const { data,
            dateTimeFromate: DATETIME_FORMATE,
            detialBaseUrl,
            regionLabels,
            statusList,
            updateStatus } = this.props
        return (
            <div className="row font-size-16 data-list">
                { data.map((item, index)=>{
                    return (
                        <div key={index}
                            className={`col-12
                            my-3
                            item
                            ${item.is_new ? 'new' : ''}
                            ${index + 1 == data.length ? '' :'border-bottom'}`}>
                            <div className="row mb-3">
                                <div className="col-4 col-sm-3 col-md-2 pr-0">
                                    <div className={`img-square-box ${item.cover_image ? '' : 'opacity-0-5'}`}>
                                        <div className="content" style={{ backgroundImage: `${item.cover_image ? `url(${item.cover_image})` : 'url(/images/logo.png)'}` }}>
                                        </div>
                                    </div>
                                </div>
                                <div className={`col-8
                                    d-flex
                                    flex-column
                                    font-size-14
                                    text-greyish-brown
                                    ${!item.is_new ? 'font-weight-normal' : 'font-weight-bold'}`}>
                                    <a className="font-size-16 text-primary line-height-22 mute-button" href={`${detialBaseUrl}/${item.id}`}>{item.title}</a>
                                    <div className="my-2">
                                        {regionLabels.map((region, index) => {
                                            return (
                                                <span key={index}>{item.regions[region]}
                                                    {`${_.size(item.regions) - 1 > index ? ', ' : ''}`}</span>
                                            )
                                        })}
                                    </div>
                                    <div className="opacity-0-5">{_.get(item, 'updated_at') && moment(item.updated_at).format(DATETIME_FORMATE)}</div>
                                    <div className="text-capitalize mt-4 mt-auto">
                                        <FieldSelect list={statusList}
                                            value={!_.isNull(item.status_id) ? item.status_id : ''}
                                            defaultOption="status"
                                            handleValueChange={(value) => { updateStatus(item.id, value) }} />
                                    </div>
                                </div>
                            </div>
                        </div>
                    )
                })}
            </div>
        )
    }

}

export default DataListMobile
