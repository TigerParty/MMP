import React from 'react'
import Avatar from '../../../components/avatar/avatar'
import { MESSAGE_TIME_FORMATE } from '../../constants'



class Log extends React.Component {
    constructor(props) {
        super(props)
        this.showContent = this.showContent.bind(this)
    }

    showContent() {
        const { message, audio } = this.props
        if(message) {
            return message
        }
        if(audio) {
            return (
              <audio controls>
                <source src={audio} type="audio/mpeg"/>
                Your browser does not support the audio element.
              </audio>
            )
        }
    }

    render() {
        const { time, isHost } = this.props
        const userTypeClass = this.props.isHost? 'host':'client'
        const timeStr = time? moment(time).format(MESSAGE_TIME_FORMATE):''
        let hostAvatar = '';
        if (isHost) {
          hostAvatar = (<div className="pl-3 align-self-center avatar-col"><Avatar/></div>);
        }
        return (
          <div className={`log
              d-flex
              px-3
              pt-3
              ${userTypeClass}`}>
            <div className="d-flex flex-column content-col">
              <div className="content
                position-relative
                p-3
                text-left">
                { this.showContent() }
              </div>
              <div className="time font-size-12 mt-1">{timeStr}</div>
            </div>
            {hostAvatar}
          </div>
        )
    }

}

export default Log
