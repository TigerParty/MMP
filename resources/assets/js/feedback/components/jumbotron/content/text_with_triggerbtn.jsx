import React from 'react'


class TextContentWithTriggerButton extends React.Component {
    constructor(props) {
        super(props)
        this.state = {
            descriptionActive: false
        }
        this.toggleJumbotronDescription = this.toggleJumbotronDescription.bind(this)
    }

    toggleJumbotronDescription() {
        const descriptionActive = this.state.descriptionActive
        this.setState({
            descriptionActive: !descriptionActive
        })
    }

    render() {
        const { title,
            description,
            hideDescriptionBtnStr,
            showDescriptionBtnStr } = this.props
        const { descriptionActive } = this.state
        const descriptionAcitve = descriptionActive ? 'active' : ''
        const descriptionContentAcitve = descriptionActive ? '' : 'text-truncate'
        const triggerBtnStr = descriptionActive ? hideDescriptionBtnStr : showDescriptionBtnStr
        return (
            <div className="row">
              <div className="col-12">
                <div className={`row description ${descriptionAcitve}`}>
                  <div className="col-12
                    text-greyish-brown
                    font-size-15
                    font-size-i7-17
                    font-size-sm-19
                    font-size-md-21
                    font-size-lg-28
                    font-weight-bold
                    letter-spacing-0-2">
                    { title }
                  </div>
                  <div className={`col-12
                    text-greyish-brown
                    font-size-12
                    font-size-md-15
                    pt-1
                    pt-lg-2
                    content
                    ${descriptionContentAcitve}`}>
                    { description }
                  </div>
                </div>
                <div className="row">
                  <div className="col-auto
                    align-self-center
                    text-primary
                    font-weight-bold
                    font-size-12
                    font-size-md-15
                    pt-2
                    description-trigger-btn
                    letter-spacing-0-2
                    d-lg-none
                    cursor-pointer"
                    onClick={ this.toggleJumbotronDescription }>
                    { triggerBtnStr } <img src="/images/icon/read-more.svg"/>
                  </div>
                </div>
              </div>
            </div>
        )
    }

}

export default TextContentWithTriggerButton
