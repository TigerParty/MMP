import React from 'react'


class MobilePagination extends React.Component {
    constructor(props) {
        super(props)
    }

    render() {
        const { isPrev, handleClick } = this.props
        return (
            <div className={`d-lg-none mobile-pagnation-btn text-white px-3 py-1 ${isPrev ? 'prev' : 'next'}`} onClick={() => { window.scrollTo(0, 0); handleClick()}}>
                {isPrev ? 'Prev Page': 'Next Page'}
            </div>
        )
    }

}

export default MobilePagination
