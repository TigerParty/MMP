import React from 'react'
import Counter from './counter/counter'


class Jumbotron extends React.Component {
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
            apparentTitle,
            content,
            counterData } = this.props
        const descriptionAcitve = this.state.descriptionActive? 'active': ''
        const descriptionContentAcitve = this.state.descriptionActive? '': 'text-truncate'
        const triggerBtnStr = this.state.descriptionActive? this.props.hideDescriptionBtnStr : this.props.showDescriptionBtnStr
        return (
          <div className="jumbotron
            jumbotron-fluid
            bg-white
            py-3
            mb-0">
            <div className="container-fluid pb-2 pb-lg-3 px-lg-4">
              <div className="row info">
                <div className="col-12 col-lg-7">
                  <div className="row">
                    <div className="col-auto
                      font-weight-bold
                      font-size-45
                      font-size-i7-50
                      font-size-sm-55
                      font-size-md-60
                      font-size-lg-80
                      text-greyish-brown
                      pr-0
                      text-uppercase
                      opacity-0-2
                      title">
                      { title }
                    </div>
                    <div className="col-auto
                      font-weight-bold
                      font-size-45
                      font-size-i7-50
                      font-size-sm-55
                      font-size-md-60
                      font-size-lg-80
                      text-primary
                      pl-2
                      count-num">
                      { apparentTitle }
                    </div>
                  </div>
                  { content }
                </div>
                <div className="d-none d-lg-block col-lg-5 pl-5">
                  <Counter
                    data={ counterData }
                     />
                </div>
              </div>
            </div>
          </div>
        )
    }

}

export default Jumbotron
