import React from 'react'
import ReactModal from 'react-modal'


class Modal extends React.Component {
    constructor(props) {
        super(props)
        this.handleCloseModal = this.handleCloseModal.bind(this)
    }

    handleCloseModal() {
        const { type, closeModal, disabledActive } = this.props
        if(disabledActive) return
        closeModal(type)
    }

    render() {
        const { value, title, bodyContent, formId, disabledActive, customClass, hiddenCancel, customCancelClass, customCancelStr } = this.props
        return (
            <ReactModal
              isOpen={value}
              contentLabel="onRequestClose Example"
              onRequestClose={this.handleCloseModal}
              className={`react-modal ${customClass}`}
              overlayClassName="react-modal-overlay">
              <div className="container-fluid text-greyish-brown font-weight-bold font-size-16">
                <div className="row
                  text-uppercase
                  py-3
                  header
                  align-items-center">
                  <div className="col-auto font-size-18">
                  {title}
                  </div>
                  <div className="col-auto
                    ml-auto
                    font-size-12
                    close-btn
                    d-flex
                    align-items-center
                    cursor-pointer"
                    onClick={this.handleCloseModal}
                    disabled={disabledActive}>
                    <span className="mr-2">close</span>
                    <img src="../images/icon/close.svg" />
                  </div>
                </div>
                <div className="row py-2">
                  { bodyContent }
                </div>
                {!hiddenCancel &&
                  <div className="row">
                    <div className="col-12 my-4 mb-lg-5 col-lg-6 offset-lg-3">
                      <div className="row text-center font-weight-normal justify-content-center">
                          <div className="col-6 text-uppercase pr-1">
                            <div className={`py-3 cursor-pointer ${customCancelClass? customCancelClass:'bg-light-grey'}`} onClick={this.handleCloseModal} disabled={disabledActive}>
                              {customCancelStr? customCancelStr: 'cancel'}
                            </div>
                          </div>

                          {formId &&
                            <div className="col-6 text-uppercase pl-1">
                              <input className="bg-primary
                                text-white
                                py-3
                                cursor-pointer
                                w-100
                                border-0
                                mute-button"
                                value="SUBMIT"
                                type="submit"
                                form={formId}
                                disabled={disabledActive}/>
                            </div>
                          }
                      </div>
                    </div>
                  </div>
                }
              </div>
            </ReactModal>
        )
    }

}

export default Modal
