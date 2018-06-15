import React from 'react'
import Header from './header'
import Item from './item/item'
import ItemMobile from './item/item_mobile'
import MobilePagination from './field/mobile_pagination'


class DataList extends React.Component {
    constructor(props) {
        super(props)
    }

    render() {
        const { data, orderStr, selectedId, updatedStatus, getDetail, rootPath, isAdmin, pagination, pageChange } = this.props

        return (
            <div className="row data-list">
                <div className="col-12 d-none d-lg-block">
                    <Header {...pagination}
                        pageChange={ pageChange }
                        order={ orderStr } />
                </div>
                <div className="col-12">
                    {
                        data.map((item, index) => {
                            return (
                                <Item
                                    fields={ item.fields }
                                    id={ item.id }
                                    isRead={ item.isRead }
                                    isSelected={ item.id==selectedId }
                                    key={ index }
                                    onClickItem={ getDetail }
                                    updatedStatus= { updatedStatus }
                                    isAdmin={ isAdmin }></Item>
                            )
                        })
                    }
                </div>
                <div className={`col-12 ${pagination.current > 1 ? 'pt-3' : ''} ${pagination.total > pagination.to ? 'pb-3' : ''}`}>
                    {pagination.current > 1 && <MobilePagination isPrev={true} handleClick={() => { pageChange(pagination.current - 1) }} />}
                    {
                        data.map((item, index) => {
                            return (
                                <ItemMobile
                                    fields={ item.fields }
                                    id={ item.id }
                                    isRead={ item.isRead }
                                    linkTo={ `${rootPath}/${item.id}`}
                                    key={ index } ></ItemMobile>
                            )
                        })
                    }
                    {pagination.total > pagination.to && <MobilePagination isPrev={false} handleClick={() => { pageChange(pagination.current + 1) }} />}
                </div>
            </div>
        )
    }

}

export default DataList
