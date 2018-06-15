import React from 'react'


class ApprovedBtn extends React.Component {
    constructor(props) {
        super(props)
    }

    render() {
        const { active, handleClick } = this.props
        return (
            <div className={`d-flex
                align-items-center
                cursor-pointer
                py-1
                pl-2
                approvedBtn
                ${active? 'active':''}`} onClick={handleClick}>
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                    <path fillRule="evenodd" d="M8 17.054l-4.9-5.17-1.633 1.724L8 20.5 22 5.73l-1.633-1.722z" />
                </svg>
                <div className="text-capitalize ml-1 text-left content">{active? 'approved':'approve'}</div>
            </div>
        )
    }

}

export default ApprovedBtn
