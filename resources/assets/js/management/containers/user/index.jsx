import React from 'react'
import { bindActionCreators } from 'redux'
import { connect } from 'react-redux'
import { withRouter } from 'react-router-dom'
import ReactModal from 'react-modal'
import 'slick-carousel/slick/slick.css'
import 'slick-carousel/slick/slick-theme.css'
import swal from 'sweetalert'
import Slider from "react-slick"
import FilterTools from '../../components/filter_tools/filter_tools'
import FilterSearch from '../../components/filter_tools/field/search'
import FilterSelect from '../../components/filter_tools/field/select'
import FilterButton from '../../components/filter_tools/field/button'
import DataListMobile from '../../components/data_list/data_list_mobile'
import DataList from '../../components/data_list/data_list'
import Empty from '../../../components/empty'
import Modal from '../../../components/modal'
import UserForm from '../../components/forms/user'
import { getUserList,
         getUser,
         updateFilter,
         cleanFilter,
         deleteUser,
         openModal,
         closeModal,
         createUser,
         updateUser,
         updateUserNotify,
         updateProjectFilter,
         cleanProjectFilter,
         getProjectList
       } from '../../actions/user'


ReactModal.setAppElement('#management')


class Index extends React.Component {
    constructor(props) {
        super(props)
        this.state = {
            formId: {
              create: 'createUserForm',
              update: 'updateUserForm'
            },
            selectedProject: ''
        }

        this.showFilterTools = this.showFilterTools.bind(this)
        this.showUserForm = this.showUserForm.bind(this)
        this.handleFilterChange = this.handleFilterChange.bind(this)
        this.handleFilterClean = this.handleFilterClean.bind(this)
        this.handleOpenEditUser = this.handleOpenEditUser.bind(this)
        this.handleOpenAddProject = this.handleOpenAddProject.bind(this)
        this.handleAddProjectToUser = this.handleAddProjectToUser.bind(this)
        this.handleSeletedProject = this.handleSeletedProject.bind(this)
        this.handleDeleteProjectFromUser = this.handleDeleteProjectFromUser.bind(this)
        this.showAddProject = this.showAddProject.bind(this)
        this.showUserRole = this.showUserRole.bind(this)
    }

    componentWillMount(){
        const { getUserList, filter, getProjectList, projectFilter } = this.props
        getUserList(filter)
        getProjectList(projectFilter)
    }

    componentWillReceiveProps(nextProps) {
        const { isSubmitting: oldIsSubmitting,
          closeModal,
          filter,
          getUserList,
          modals,
          getUser,
          selectedUser: currentSelectedUser } = this.props
        const { isSubmitting, errors, selectedUser } = nextProps
        if(oldIsSubmitting && !isSubmitting && _.isEmpty(errors) && !modals.addProject) {
            const modalType = selectedUser? 'update':'create'
            closeModal(modalType)
            getUserList(filter)
        }

        if(oldIsSubmitting && !isSubmitting && _.isEmpty(errors) && modals.addProject) {
            getUser(currentSelectedUser.id)
        }
    }

    componentDidUpdate(prevProps, prevState) {
        const { filter: oldFilter, projectFilter: oldProjectFilter } = prevProps
        const { filter, getUserList, projectFilter, getProjectList } = this.props
        if(!_.isEqual(filter, oldFilter)) {
          getUserList(filter)
        }

        if(!_.isEqual(projectFilter, oldProjectFilter)) {
          getProjectList(projectFilter)
        }
    }

    handleFilterChange(type="keyword", value=null) {
        const { updateFilter, updateProjectFilter } = this.props
        if(type=="project"){
          updateProjectFilter(value)
        }else{
          updateFilter(type, value)
        }
    }

    handleFilterClean(){
        this.props.cleanFilter()
    }

    handleOpenEditUser(userId) {
        const { openModal } = this.props
        openModal("update", userId)
    }

    handleOpenAddProject(userId) {
        const { openModal, getUser, cleanProjectFilter } = this.props
        this.setState({
          selectedProject: ''
        })
        cleanProjectFilter()
        getUser(userId)
        openModal("addProject", userId)
    }

    handleSeletedProject(type, projectId) {
        this.setState({
          selectedProject: projectId
        })
    }

    handleAddProjectToUser() {
        const { selectedUser, updateUser } = this.props
        const { selectedProject } = this.state
        let project_ids = _.map(selectedUser.projects, 'id')
        project_ids.push(selectedProject)
        updateUser({project_ids: project_ids }, selectedUser.id)
    }

    handleDeleteProjectFromUser(removeProjectId) {
        swal({
          title: "Are you sure?",
          text: "Once deleted, you will not be able to recover it!",
          icon: "warning",
          buttons: true,
          dangerMode: true
        }).then((willDelete) => {
          if (willDelete) {
            const { selectedUser, updateUser } = this.props
            const { selectedProject } = this.state
            let project_ids = _.map(selectedUser.projects, 'id')
            project_ids = _.remove(project_ids, (id) => {
              return id != removeProjectId
            })
            updateUser({ project_ids: project_ids }, selectedUser.id)
          }
        })
    }

    showFilterTools() {
        const { permissionList, userList, filter, pagination } = this.props
        const search = (
            <div className="col-12
              col-lg-3
              pr-lg-0
              order-lg-2"
              key="filter_search">
              <FilterSearch value={filter.keyword} type="keyword" valueChange={this.handleFilterChange} />
            </div>
        )
        const select = (
            <div className="col-12
              col-lg-2
              pr-lg-0
              order-lg-1
              mt-2
              mt-lg-0"
              key="filter_select_user_type">
              <FilterSelect
                list={permissionList}
                value={filter.permission_level_id}
                type="permission_level_id"
                valueChange={this.handleFilterChange}
                defaultOption="User Type" />
            </div>
        )

        const clear = (
            <div className="col-12
              col-lg-auto
              order-lg-3
              mt-3
              mt-lg-0
              align-self-center
              text-center"
              key="filter_clear_btn">
             <FilterButton handleOnClick={this.handleFilterClean} btnStr="clear" />
            </div>
        )

        const paginationData = {
          pagination,
          currentPage: filter.page,
          pageChange: this.handleFilterChange,
          type: "page"
        }

        return (
          <FilterTools filters={[search, select, clear]} pagination={paginationData} />
        )
    }

    showUserForm(formId, type) {
        const { permissionList, createUser, updateUser, errors, closeModal, selectedUser} = this.props
        return (
          <UserForm formId={formId}
              submitSuccess={ closeModal }
              submitFrom={ type=='create'? createUser: updateUser}
              permissionList={ permissionList }
              errors={errors}
              selectedUser={selectedUser} />
        )
    }

    showUserRole() {
        const { closeModal } = this.props
        const config = {
          dots: true,
          slidesToShow: 1,
          slidesToScroll: 1,
          centerMode: true,
          arrows: false,
          speed: 500,
          centerPadding: "20px",
          infinite: false,
        }

        return (
          <div className="col-12">
            <div className="row d-lg-none">
              <div className="col-12">
                <Slider {...config}>
                  <div className="px-2 h-100">
                    <div className="card
                      bg-white
                      h-100
                      border-0
                      rounded-0
                      text-center">
                      <div className="card-body">
                        <div className="card-title
                          text-center
                          font-size-24
                          font-weight-bold
                          text-capitalize
                          d-flex
                          flex-column
                          flex-xl-row
                          justify-content-center
                          align-items-center">
                          <div className="mr-xl-2">public</div>
                          <div className="d-flex">
                            <img className="scale-1-3" src="../images/icon/star.svg" />
                          </div>
                        </div>
                        <img className="py-4 ml-auto mr-auto" src="../images/user_type/public.svg" />
                        <p className="card-text
                          text-greyish-brown
                          opacity-0-5
                          my-2">
                          Accesses to limited information
                        </p>
                      </div>
                    </div>
                  </div>
                  <div className="px-2 h-100">
                    <div className="card
                      bg-white
                      h-100
                      border-0
                      rounded-0
                      text-center">
                      <div className="card-body">
                        <div className="card-title
                          text-center
                          font-size-24
                          font-weight-bold
                          text-capitalize
                          d-flex
                          flex-column
                          flex-xl-row
                          justify-content-center
                          align-items-center">
                          <div className="mr-xl-2">reporter</div>
                          <div className="d-flex">
                            <img className="mr-2 scale-1-3" src="../images/icon/star.svg" />
                            <img className="scale-1-3" src="../images/icon/star.svg" />
                          </div>
                        </div>
                        <img className="py-4 ml-auto mr-auto" src="../images/user_type/reporter.svg" />
                        <p className="card-text
                          text-greyish-brown
                          opacity-0-5
                          my-2">
                          Collects and reports data
                        </p>
                      </div>
                    </div>
                  </div>
                  <div className="px-2 h-100">
                    <div className="card
                      bg-white
                      h-100
                      border-0
                      rounded-0
                      text-center">
                      <div className="card-body">
                        <div className="card-title
                          text-center
                          font-size-24
                          font-weight-bold
                          text-capitalize
                          d-flex
                          flex-column
                          flex-xl-row
                          justify-content-center
                          align-items-center">
                          <div className="mr-xl-2">coordinator</div>
                          <div className="d-flex">
                            <img className="mr-2 scale-1-3" src="../images/icon/star.svg" />
                            <img className="mr-2 scale-1-3" src="../images/icon/star.svg" />
                            <img className="scale-1-3" src="../images/icon/star.svg" />
                          </div>
                        </div>
                        <img className="py-4 ml-auto mr-auto" src="../images/user_type/coordinator.svg" />
                        <p className="card-text
                          text-greyish-brown
                          opacity-0-5
                          my-2">
                          Validates all the data collected by reporters
                        </p>
                      </div>
                    </div>
                  </div>
                  <div className="px-2 h-100">
                    <div className="card bg-white h-100 border-0 rounded-0 text-center">
                      <div className="card-body">
                        <div className="card-title
                          text-center
                          font-size-24
                          font-weight-bold
                          text-capitalize
                          d-flex
                          flex-column
                          flex-xl-row
                          justify-content-center
                          align-items-center">
                          <div className="mr-xl-2">admin</div>
                          <div className="d-flex">
                            <img className="mr-2 scale-1-3" src="../images/icon/star.svg" />
                            <img className="mr-2 scale-1-3" src="../images/icon/star.svg" />
                            <img className="mr-2 scale-1-3" src="../images/icon/star.svg" />
                            <img className="scale-1-3" src="../images/icon/star.svg" />
                          </div>
                        </div>
                        <img className="py-4 ml-auto mr-auto" src="../images/user_type/admin.svg" />
                        <p className="card-text
                          text-greyish-brown
                          opacity-0-5
                          my-2">
                          Adds and verifies coordinators
                        </p>
                      </div>
                    </div>
                  </div>
                </Slider>
              </div>
            </div>
            <div className="row my-4 mb-5 d-lg-none">
              <div className="col-12 my-4 mb-lg-5 col-lg-6 offset-lg-3">
                <div className="row text-center font-weight-normal">
                    <div className="col-12">
                      <div className="bg-primary
                        py-3
                        cursor-pointer
                        text-uppercase
                        text-white"
                        onClick={ ()=>{ closeModal('userRoles') }}>
                        got it!
                      </div>
                    </div>
                </div>
              </div>
            </div>
            <div className="row
              py-5
              text-greyish-brown
              d-none
              d-lg-flex">
              <div className="col-3">
                <div className="card
                  bg-white
                  h-100
                  border-0
                  rounded-0
                  text-center">
                  <div className="card-body">
                    <div className="card-title
                      text-center
                      font-size-24
                      font-weight-bold
                      text-capitalize
                      d-flex
                      flex-column
                      flex-xl-row
                      justify-content-center
                      align-items-center">
                      <div className="mr-xl-2">public</div>
                      <div className="d-flex">
                        <img className="scale-1-3" src="../images/icon/star.svg" />
                      </div>
                    </div>
                    <img className="py-4" src="../images/user_type/public.svg" />
                    <p className="card-text
                      text-greyish-brown
                      opacity-0-5
                      my-2">
                      Accesses to limited information
                    </p>
                  </div>
                </div>
              </div>
              <div className="col-3">
                <div className="card
                  bg-white
                  h-100
                  border-0
                  rounded-0
                  text-center">
                  <div className="card-body">
                    <div className="card-title
                      text-center
                      font-size-24
                      font-weight-bold
                      text-capitalize
                      d-flex
                      flex-column
                      flex-xl-row
                      justify-content-center
                      align-items-center">
                      <div className="mr-xl-2">reporter</div>
                      <div className="d-flex">
                        <img className="mr-2 scale-1-3" src="../images/icon/star.svg" />
                        <img className="scale-1-3" src="../images/icon/star.svg" />
                      </div>
                    </div>
                    <img className="py-4" src="../images/user_type/reporter.svg" />
                    <p className="card-text
                      text-greyish-brown
                      opacity-0-5
                      my-2">
                      Collects and reports data
                    </p>
                  </div>
                </div>
              </div>
              <div className="col-3">
                <div className="card
                  bg-white
                  h-100
                  border-0
                  rounded-0
                  text-center">
                  <div className="card-body">
                    <div className="card-title
                      text-center
                      font-size-24
                      font-weight-bold
                      text-capitalize
                      d-flex
                      flex-column
                      flex-xl-row
                      justify-content-center
                      align-items-center">
                      <div className="mr-xl-2">coordinator</div>
                      <div className="d-flex">
                        <img className="mr-2 scale-1-3" src="../images/icon/star.svg" />
                        <img className="mr-2 scale-1-3" src="../images/icon/star.svg" />
                        <img className="scale-1-3" src="../images/icon/star.svg" />
                      </div>
                    </div>
                    <img className="py-4" src="../images/user_type/coordinator.svg" />
                    <p className="card-text
                      text-greyish-brown
                      opacity-0-5
                      my-2">
                      Validates all the data collected by reporters
                    </p>
                  </div>
                </div>
              </div>
              <div className="col-3">
                <div className="card bg-white h-100 border-0 rounded-0 text-center">
                  <div className="card-body">
                    <div className="card-title
                      text-center
                      font-size-24
                      font-weight-bold
                      text-capitalize
                      d-flex
                      flex-column
                      flex-xl-row
                      justify-content-center
                      align-items-center">
                      <div className="mr-xl-2">admin</div>
                      <div className="d-flex">
                        <img className="mr-2 scale-1-3" src="../images/icon/star.svg" />
                        <img className="mr-2 scale-1-3" src="../images/icon/star.svg" />
                        <img className="mr-2 scale-1-3" src="../images/icon/star.svg" />
                        <img className="scale-1-3" src="../images/icon/star.svg" />
                      </div>
                    </div>
                    <img className="py-4" src="../images/user_type/admin.svg" />
                    <p className="card-text
                      text-greyish-brown
                      opacity-0-5
                      my-2">
                      Adds and verifies coordinators
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>)
    }

    showAddProject() {
        const { filter, projectFilter, projectList, selectedUser } = this.props
        const { selectedProject } = this.state
        const search = (
              <div className="col-12
                col-lg-4
                pr-lg-0
                mt-3
                mt-lg-0"
                key="filter_search_region">
                <FilterSearch
                  value={projectFilter}
                  type="project"
                  valueChange={this.handleFilterChange} />
              </div>
          )
        const select = (
              <div className="col-12
                col-lg-4
                pr-lg-0
                mt-2
                mt-lg-0"
                key="project_list">
                <FilterSelect
                  list={projectList}
                  type="project_list"
                  value={selectedProject}
                  valueChange={this.handleSeletedProject}
                  defaultOption={lang.management.user.fields.project} />
              </div>
          )
        const add = (
              <div className="col-12
                col-lg-auto
                order-lg-3
                my-2
                my-lg-0
                align-self-center
                text-center
                cursor-pointer"
                disabled={ selectedProject.length < 1 }
                key="add_btn">
                <div className="border border-greyish-brown px-3 d-flex align-items-center justify-content-center" style={{height: '40px'}}>
                  <FilterButton handleOnClick={this.handleAddProjectToUser} btnStr="add to user" />
                </div>
              </div>
          )

        return (
          <div className="col-12">
            <FilterTools filters={[search, select, add]} bgDark={true}/>
            <div className="row data-list">
              <div className="col-12 opacity-0-5 font-size-22 text-capitalize">
                  {lang.management.user.fields.project}
              </div>
              {_.has(selectedUser, 'projects') && selectedUser.projects.map((item, Index) => {
                return (<div className="col-12 border-top border-grey-blue">
                  <div className="row">
                    <div className="col-8 align-self-center text-truncate">{item.title}</div>
                    <div className="col-4">
                      <div className="d-flex
                        justify-content-end
                        align-items-center
                        py-2
                        cursor-pointer
                        delete-btn"
                        onClick={() => { this.handleDeleteProjectFromUser(item.id)}}>
                        <img src="../images/icon/delete-black.svg"/>
                        <span className="pl-2 font-weight-normal">Delete</span>
                      </div>
                    </div>
                  </div>
                </div>)
              })}
              { _.has(selectedUser, 'projects') && selectedUser.projects.length==0 && (
                  <div className=" col-12 text-center  border-top border-grey-blue">
                    <Empty />
                  </div>
                )
              }
            </div>
          </div>)
    }

    render() {
        const { userList, permissionList, createUser, deleteUser, modals, openModal, closeModal, isSubmitting, updateUserNotify } = this.props
        const { formId } = this.state
        return (
          <div className="row
            bg-white
            mx-lg-0
            mt-3">
            <div className="col-12
              col-lg-auto
              text-capitalize
              font-weight-bold
              font-size-35
              font-size-md-45
              font-size-lg-50
              text-primary
              text-lg-grey
              py-2">
              {lang.management.user.title}
            </div>
            <div className="col-lg-4
              ml-auto
              align-self-center
              d-none
              d-lg-block">
              <div className="row text-center function-tools font-size-lg-16">
                  <div className="col-6 text-uppercase pr-1 pr-lg-2">
                    <div className="bg-light-grey
                      py-3
                      font-weight-bold
                      cursor-pointer"
                      onClick={()=>{ openModal('userRoles') }}>
                      {lang.management.user.function.show_user_role}
                    </div>
                  </div>
                  <div className="col-6 text-uppercase pl-1 pl-lg-2">
                    <div className="bg-primary
                      text-white
                      py-3
                      cursor-pointer"
                      onClick={()=>{ openModal("create") }}>
                      {lang.management.user.function.add_user}
                    </div>
                  </div>
              </div>
            </div>
            <div className="col-12">
              { this.showFilterTools() }
            </div>
            {userList?
              (userList.length > 0?
                <div className="col-12">
                  <div className="row">
                    <div className="col-12  d-lg-none">
                      <DataListMobile
                        data={userList}
                        permissionList={permissionList}
                        handleEdit={this.handleOpenEditUser}
                        handleAdd={this.handleOpenAddProject}
                        openModal={openModal}
                        handleDelete={deleteUser}
                        handleNotifySwitch={updateUserNotify}/>
                    </div>
                    <div className="col-12 font-size-16 d-none d-lg-block">
                      <DataList data={userList}
                        permissionList={permissionList}
                        handleEdit={this.handleOpenEditUser}
                        handleAdd={this.handleOpenAddProject}
                        handleDelete={deleteUser}
                        handleNotifySwitch={updateUserNotify}/>
                    </div>
                  </div>
                </div>:
                <div className="col-12">
                    <Empty />
                </div>)
              :""
            }
            <div className="col-12 my-4 d-lg-none">
              <div className="row text-center function-tools">
                  <div className="col-6 text-uppercase pr-1 ">
                    <div className="bg-light-grey py-3 cursor-pointer" onClick={()=>{ openModal('userRoles') }}>
                      {lang.management.user.function.show_user_role}
                    </div>
                  </div>
                  <div className="col-6 text-uppercase pl-1">
                    <div className="bg-primary
                      text-white
                      py-3
                      cursor-pointer"
                      onClick={()=>{ openModal('create') }}>
                      {lang.management.user.function.add_user}
                    </div>
                  </div>
              </div>
            </div>

            <Modal value={modals.update}
              title={lang.management.user.modals.edit_user.title}
              bodyContent={this.showUserForm(formId.update, 'update')}
              type="update"
              formId={ formId.update }
              closeModal={closeModal}
              disabledActive={isSubmitting} />

            <Modal value={modals.create}
              title={lang.management.user.modals.add_user.title}
              bodyContent={this.showUserForm(formId.create, 'create')}
              type="create"
              formId={ formId.create }
              closeModal={closeModal}
              disabledActive={isSubmitting} />

            <Modal value={modals.addProject}
              title={lang.management.user.modals.add_project.title}
              bodyContent={this.showAddProject()}
              type="addProject"
              closeModal={closeModal}
              disabledActive={isSubmitting}
              customCancelClass="bg-primary text-white"
              customCancelStr="close" />

            <Modal value={modals.userRoles}
              title={lang.management.user.modals.user_role.title}
              bodyContent={this.showUserRole()}
              type="userRoles"
              closeModal={closeModal}
              customClass="large bg-light-grey"
              hiddenCancel={true} />
          </div>

        )
    }
}

const mapStateToProps = state => {
    const { auth, user } = state
    return {
        editPermission: auth.isAdmin,
        userList: user.list,
        permissionList: user.permissions,
        filter: user.filter,
        pagination: user.pagination,
        modals: user.modals,
        errors: user.errors,
        isSubmitting: user.isSubmitting,
        selectedUser: user.selectedUser,
        projectFilter: user.projectFilter,
        projectList: user.projectList
    }
}

const mapDispatchToProps = dispatch => {
    return {
        dispatch,
        getUserList: bindActionCreators(getUserList, dispatch),
        updateFilter: bindActionCreators(updateFilter, dispatch),
        cleanFilter: bindActionCreators(cleanFilter, dispatch),
        createUser: bindActionCreators(createUser, dispatch),
        updateUser: bindActionCreators(updateUser, dispatch),
        deleteUser: bindActionCreators(deleteUser, dispatch),
        openModal: bindActionCreators(openModal, dispatch),
        closeModal: bindActionCreators(closeModal, dispatch),
        updateUserNotify: bindActionCreators(updateUserNotify, dispatch),
        getUser: bindActionCreators(getUser, dispatch),
        updateProjectFilter: bindActionCreators(updateProjectFilter, dispatch),
        cleanProjectFilter: bindActionCreators(cleanProjectFilter, dispatch),
        getProjectList: bindActionCreators(getProjectList, dispatch)
    }
}

export default withRouter(connect(mapStateToProps, mapDispatchToProps)(Index))
