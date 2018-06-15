import React from 'react'
import { bindActionCreators } from 'redux'
import { connect } from 'react-redux'
import { withRouter } from 'react-router-dom'
import ReactModal from 'react-modal'
import Counter from '../components/counter'
import FunctionButton from '../components/function_tools/button'
import FilterSearch from '../components/filter_tools/search'
import FilterSelect from '../components/filter_tools/select'
import FilterInput from '../components/filter_tools/input'
import FilterPagination from '../components/filter_tools/pagination'
import DataListMobile from '../components/data_list/data_list_mobile'
import DataList from '../components/data_list/data_list'
import { updateFilter, getProjectList, openModal, deleteProject, updateProjectStatus, updateProjectApproval } from '../actions/admin'
import { getRegionList } from '../actions/region'
import Empty from '../../components/empty'
import { PROJECT_DATETIME_FORMATE } from '../constants/index'
import { constants } from 'zlib';

ReactModal.setAppElement('#admin')


class Index extends React.Component {
    constructor(props) {
        super(props)
		const BASE_URL = _.get(constants, 'BASE_URL', '')
		this.state = {
			showMoreFilterContent: false,
			projectDetialBaseUrl: `${BASE_URL}/admin/project`,
			filterValues: {}
		}
		this.handleOpenModal = this.handleOpenModal.bind(this)
		this.toggleMoreFilterContent = this.toggleMoreFilterContent.bind(this)
		this.handleItemEdit = this.handleItemEdit.bind(this)
		this.updateFilterValues = this.updateFilterValues.bind(this)
    }

	componentWillMount() {
		const { filter, getProjectList, getRegionList } = this.props
		getProjectList(filter)
		getRegionList(null)
	}

	componentDidMount() {
		document.addEventListener('click', (e)=>{
			if (this.state.showMoreFilterContent){
				const elm = e.target
				const filterMoretarget = this.refs.filterMoreContent
				const filterMoreBtnTarget = this.refs.filterMoreBtn
				if (elm !== filterMoretarget && elm !== filterMoreBtnTarget && !filterMoretarget.contains(elm) && !filterMoreBtnTarget.contains(elm)) {
					this.toggleMoreFilterContent()
				}
			}
		}, this)
	}

	componentWillReceiveProps(nextProps) {
		const { filter: newFilter } = nextProps
		const { filter, getRegionList, regionLabels } = this.props
		if (!_.isEqual(newFilter.regions, filter.regions) &&
			newFilter.regions.length < regionLabels.length){
			getRegionList(_.last(newFilter.regions))
		}
	}

	componentDidUpdate(prevProps, prevState) {
		const { filter: oldFilter} = prevProps
		const { filter, getProjectList } = this.props
		if (!_.isEqual(filter, oldFilter)) {
			getProjectList(filter)
		}
	}

	handleOpenModal(type) {
		const { openModal } = this.props
	}

	toggleMoreFilterContent() {
		const { showMoreFilterContent: oldShowMoreFilterContent } = this.state
		this.setState({ showMoreFilterContent: !oldShowMoreFilterContent })
	}

	handleItemEdit(id) {
		const { projectDetialBaseUrl } = this.state

		window.location = `${projectDetialBaseUrl}/${id}/edit`
	}

	updateFilterValues(type, value, formFildId) {
		const updateFilterValues = JSON.parse(JSON.stringify(this.state.filterValues))

		if ( formFildId ){
			if (value && value.length>0){
				updateFilterValues[formFildId] = value
			}else {
				delete updateFilterValues[formFildId]
			}
			this.setState({ filterValues: updateFilterValues})
		}else {
			this.setState({ filterValues: {} })
			this.toggleMoreFilterContent()
		}
	}

    render() {
		const { filter,
			updateFilter,
			pagination,
			projectList,
			regionLabels,
			regionList,
			filterOptions,
			statusList,
			deleteProject,
			counterInfo,
			updateProjectStatus,
			updateProjectApproval } = this.props
		const { projectDetialBaseUrl } = this.state
        return (
			<div className="row
				bg-white
				mx-lg-0
				mt-3">
				<div className="col-12
					col-lg-auto
					text-uppercase
					font-weight-bold
					font-size-35
					font-size-sm-40
					font-size-md-50
					font-size-lg-65
					text-primary
					py-2">
					<div className="d-flex flex-wrap align-items-center">
						<div>{lang.admin.project.title}</div>
						<div className="ml-auto ml-lg-3 d-flex">
							<Counter value={counterInfo.new} subTitle="new" needdividerLine={true} />
							<Counter value={counterInfo.total} subTitle="total" />
						</div>
					</div>
				</div>
				<div className="col-lg-6
					col-xl-4
					ml-auto
					align-self-center
					d-none
					d-lg-block">
					<FunctionBottons projectDetialBaseUrl={projectDetialBaseUrl} />
				</div>
				<div className="col-12 my-3 mt-lg-0">
					{/* ====Filter Mobile==== */}
					<div className="row justify-content-center d-lg-none filter">
						<div className="col-8 col-sm-5 col-md-4 bg-yellow-tan rounded-content">
							<div className="row">
								<div className="col-6
									position-relative
									divider-line
									cursor-pointer">
									<FilterSearch
										value={filter.keyword}
										type="keyword"
										handleValueChange={updateFilter}/>
								</div>
								<div className="col-6
									d-flex
									align-self-center
									justify-content-center
									cursor-pointer">
									<img src="../images/icon/filter.svg" />
									<span className="ml-3">Filters</span>
								</div>
							</div>
						</div>
					</div>
					{/* ====Filter Destop==== */}
					<div className="row d-none d-lg-block filter">
						<div className="col-12 bg-yellow-tan py-3">
							<div className="row align-items-center">
								<div className="col-3">
									<FilterSearch
										value={filter.keyword}
										type="keyword"
										handleValueChange={updateFilter} />
								</div>
								<div className="col-auto font-weight-bold font-size-14 px-0">
									Filters:
								</div>
								<div className="col-6">
									<div className="row">
										{/* ====Regions Filter====  */}
										{regionLabels.map((label, index)=>{
											let list = []
											if (filter.regions.length >= index){
												let tempRegionList = regionList
												for (let i = 0; i < index; i++) {
													const tempIndex = _.findIndex(tempRegionList, ['id', parseInt(filter.regions[i])])
													if (tempIndex > -1 && tempRegionList[tempIndex].hasOwnProperty('subregions')) {
														tempRegionList = tempRegionList[tempIndex].subregions
													}else {
														tempRegionList = []
													}
												}
												list = _.filter(tempRegionList, ['label_name', label])
											}
											return (
												<div className="col pr-0" key={index}>
													<FilterSelect
														list={list}
														value={filter.regions[index]}
														optionalKey={index}
														type="regions"
														displayKey="name"
														valueKey="id"
														defaultOption={_.startCase(label)}
														handleValueChange={updateFilter} />
												</div>
											)
										})}
										{/* ====More Filter==== */}
										<div className="col pr-0">
											<div className={`more-btn
												d-flex
												align-items-center
												px-2
												cursor-pointer
												${this.state.showMoreFilterContent ? 'active' : ''}`}
												onClick={this.toggleMoreFilterContent}
												ref="filterMoreBtn">
												<span>More</span>
												<img className="ml-auto" src="../images/icon/dropdown-triangle.svg"/>
											</div>
											<div className={`p-3
												more-btn-content
												${filterOptions.length > 3 ? 'w-100vw' : ''}
												${!this.state.showMoreFilterContent? 'd-none' : ''}`}
												ref="filterMoreContent">
												<div className={`filter-box d-inline-flex ${filterOptions.length > 3 ? 'flex-wrap' : ''}`}>
													{
														filterOptions.map((item, index) => {
															return (
																<div className="option px-2 d-flex flex-column" key={`addmore_${index}`}>
																	<div className="font-size-15 font-weight-bold mb-2">
																		{item.name}
																	</div>
																	<div className="mt-auto">
																		{item.template == 'text_box'?
																			<FilterInput
																				value={this.state.filterValues[item.id]}
																				optionalKey={item.id}
																				type="values"
																				handleValueChange={this.updateFilterValues} />:''}
																		{item.template == 'drop_down_list' ?
																			<FilterSelect
																				list={item.options}
																				value={this.state.filterValues[item.id]}
																				optionalKey={item.id}
																				type="values"
																				defaultOption="Choose"
																				handleValueChange={this.updateFilterValues} /> : ''}
																		{item.template == 'numerical' ?
																			<FilterInput
																				inputType="number"
																				value={this.state.filterValues[item.id]}
																				optionalKey={item.id}
																				type="values"
																				handleValueChange={this.updateFilterValues}
																				/> : ''}
																	</div>
																</div>
															)
														})
													}
												</div>
												<div className="filter-buttons px-2 mt-3 ml-auto pull-right">
													<FilterButtons
														updateFilterValues={this.updateFilterValues}
														applyFilters={updateFilter}
														values={this.state.filterValues}
														toggleMoreFilterContent={this.toggleMoreFilterContent}/>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div className="col-auto ml-auto">
									<FilterPagination
										data={pagination}
										currentPage={filter.page}
										type="page"
										handlePageChange={updateFilter} />
								</div>
							</div>
						</div>
					</div>
				</div>
				<div className="col-12 mb-3 d-none">
					<span className="font-size-15 font-weight-bold">Sort by:</span>
					<span className="font-size-15 font-weight-bold text-primary ml-2">Latest Date</span>
				</div>
				{projectList ?
					(projectList.length > 0 ?
						<div className="col-12">
							<div className="row">
								<div className="col-12 d-lg-none">
									<DataListMobile
										data={projectList}
										detialBaseUrl={projectDetialBaseUrl}
										dateTimeFromate={PROJECT_DATETIME_FORMATE}
										regionLabels={regionLabels}
										statusList={statusList}
										updateStatus={updateProjectStatus} />
								</div>
								<div className="col-12 d-none d-lg-block">
									<DataList
										data={projectList}
										detialBaseUrl={projectDetialBaseUrl}
										dateTimeFromate={PROJECT_DATETIME_FORMATE}
										regionLabels={regionLabels}
										statusList={statusList}
										handleEdit={this.handleItemEdit}
										handleDelete={deleteProject}
										updateStatus={updateProjectStatus}
										updateApproval={updateProjectApproval} />
								</div>
							</div>
						</div>:
						<div className="col-12">
							<Empty />
						</div>)
				:""}

				<div className="col-12 d-lg-none my-4">
					<FunctionBottons projectDetialBaseUrl={projectDetialBaseUrl}/>
				</div>

			</div>

        )
    }
}

const FunctionBottons = (props) =>{
	const { projectDetialBaseUrl } = props
	return (<div className="row
				text-center
				font-weight-bold
				font-size-lg-16
				text-uppercase">
				<div className="col-6  pr-1 pr-lg-2">
					<FunctionButton title={lang.admin.project.function.batch_entry}
						handleClick={() => { window.location = `${projectDetialBaseUrl}/create/batch`}}
						passValue="batch_entry"
						customClass="bg-light-grey"
						buttonHeight="50px"/>
				</div>
				<div className="col-6
					pl-lg-2
					text-white
					font-weight-normal">
					<FunctionButton title={lang.admin.project.function.create}
						handleClick={() => { window.location = `${projectDetialBaseUrl}/create` }}
						passValue="create"
						customClass="bg-primary"
						buttonHeight="50px" />
				</div>
			</div>)
}

const FilterButtons = (props) =>{
	const { updateFilterValues,
		applyFilters,
		values,
		toggleMoreFilterContent } = props
	return (<div className="row
				text-center
				font-weight-bold
				font-size-lg-16
				text-uppercase">
				<div className="col-6  pr-1 pr-lg-2">
					<FunctionButton title="cancel"
						handleClick={()=>{updateFilterValues()}}
						passValue="batch_entry"
						customClass="bg-light-grey"/>
				</div>
				<div className="col-6
					pl-lg-2
					text-white
					font-weight-normal">
					<FunctionButton title="apply filters"
						handleClick={() => { applyFilters('values', values); toggleMoreFilterContent();}}
						passValue="create"
						customClass="bg-primary" />
				</div>
			</div>)
}


const mapStateToProps = state => {
    const { auth, admin, region } = state
    return {
		filter: admin.filter,
		pagination: admin.pagination,
		projectList: admin.projectList,
		regionLabels: region.labels,
		regionList: region.list,
		filterOptions: admin.filterOptions,
		statusList: admin.statusList,
		counterInfo: admin.counter
    }
}

const mapDispatchToProps = dispatch => {
    return {
        dispatch,
		updateFilter: bindActionCreators(updateFilter, dispatch),
		getProjectList: bindActionCreators(getProjectList, dispatch),
		openModal: bindActionCreators(openModal, dispatch),
		getRegionList: bindActionCreators(getRegionList, dispatch),
		deleteProject: bindActionCreators(deleteProject, dispatch),
		updateProjectStatus: bindActionCreators(updateProjectStatus, dispatch),
		updateProjectApproval: bindActionCreators(updateProjectApproval, dispatch)
    }
}

export default withRouter(connect(mapStateToProps, mapDispatchToProps)(Index))
