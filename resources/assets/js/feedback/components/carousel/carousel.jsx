import React from 'react'
import Item from './item'
import Control from './control'
import Indicator from './indicator'


class Carousel extends React.Component {
    constructor(props) {
        super(props)
    }

    render() {
        const { data, needIndicator, needControl, id } = this.props
        return (
          <div>
            <div id={id} className="carousel slide" data-ride="carousel" data-interval="false">
              { needIndicator && <Indicator targetId={ id } total={ data.length } /> }

              <div className="carousel-inner">
                {   data.map((item, index) => {
                        return (
                            <Item isAcitve={ index == 0 }
                                key={ index }
                                attachmentId={ item.id }
                                description={ item.pivot.description }
                                path={ "/file/"+item.id } />
                        )
                    })
                }
              </div>
              {
                needControl &&
                ( <div>
                    <Control isPrev={ true } targetId={ id } />
                    <Control isNext={ true } targetId={ id } />
                  </div> )
              }
            </div>
          </div>
        )
    }

}

export default Carousel
