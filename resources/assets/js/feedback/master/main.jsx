import React from 'react'
import { connect } from 'react-redux'
import Header from '../../components/master/header/header'
import SubNavigation from '../components/navigation/sub_navigation'


class Main extends React.Component {
    constructor(props) {
        super(props)
    }

    render() {
        const { children, isLogin } = this.props

        return (
          <div>
            <Header isLogin={ isLogin }>
            </Header>
            <div className="container-fluid app-content">
              <div className="row">
                <div className="col-12
                  col-lg-2
                  left-sidebar-wrapper
                  bg-secondary">
                  <SubNavigation/>
                </div>
                <div className="col-12 offset-lg-2 col-lg-10 px-0 px-lg-4">
                  <main>
                  {children}
                  </main>
                </div>
              </div>
            </div>
          </div>
        )
    }
}


const mapStateToProps = state => {
    const { auth } = state
    return {
        isLogin: auth.isAdmin
    }
}

export default connect(mapStateToProps)(Main)

