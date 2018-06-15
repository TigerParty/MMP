import React from 'react'
import Pagination from './pagination'

class FilterTools extends React.Component {
    constructor(props) {
        super(props)
    }

    render() {
        const { filters, pagination, bgDark } = this.props
        return (
            <div className={`row mb-3 py-lg-3 justify-content-center filter-tools ${bgDark? 'dark': ''}`}>
              {filters.map((filter, index)=> {return (filter)})}

              { pagination &&
                (<div className="col-lg-auto ml-auto d-none d-lg-block order-lg-5">
                  <Pagination {...pagination}/>
                </div>) }
            </div>
        )
    }

}

export default FilterTools
