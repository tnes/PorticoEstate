var selectedAutocompleteValue = false;
var selectedTown = false;
var viewmodel;
var months = ["Januar", "Februar", "Mars", "April", "Mai", "Juni", "July", "August", "September", "Oktober", "November", "Desember"];
var urlParams = [];
var autocompleteData = [];
var towns = [];

CreateUrlParams(window.location.search);

//var baseURL = document.location.origin + "/" + window.location.pathname.split('/')[1] + "/bookingfrontend/";
var baseURL = strBaseURL.split('?')[0] + "bookingfrontend/";

function ViewModel()
{
	let self = this;

	self.goToBuilding = function (event) { window.location = event.building_url(); };
	self.goToOrganization = function (event) { window.location = event.org_url(); }
	self.goToEvents = function (event) { window.location = baseURL + '?menuaction=bookingfrontend.uieventsearch.show'; }

	self.toggleTown = function (event) {
		event.showTown(!event.showTown());
	};
	self.toggleFacility = function (event) {
		event.showFacility(!event.showFacility());
	};
	self.toggleActivity = function (event) {
		event.showActivity(!event.showActivity());
	};
	self.toggleGear = function (event) {
		event.showGear(!event.showGear());
	};
	self.toggleCapacity = function (event) {
		event.showCapacity(!event.showCapacity());
	};

	self.items = ko.observableArray();
	self.events = ko.observableArray([]);
	self.resources = ko.observableArray([]);
	self.towns = ko.observableArray();
	self.facilities = ko.observableArray([]);
	self.activities = ko.observableArray([]);
	self.gear = ko.observableArray([]);
	self.capacities = ko.observableArray([]);

	self.showEvents = ko.observable(true);
	//self.showResults = ko.observable(true);
	self.showSearchText = ko.observable(false);
	self.showTown = ko.observable(true);
	self.showFacility = ko.observable(true);
	self.showActivity = ko.observable(false);
	self.showGear = ko.observable(false);
	self.showCapacity = ko.observable(false);

	self.selectedTowns = ko.observableArray([]);
	self.selectedTownIds = ko.observableArray([]);
	self.selectedFacilities = ko.observableArray([]);
	self.selectedFacilityIds = ko.observableArray([]);
	self.selectedActivities = ko.observableArray([]);
	self.selectedActivityIds = ko.observableArray([]);
	self.selectedGear = ko.observableArray([]);
	self.selectedGearIds = ko.observableArray([]);
	self.selectedCapacities = ko.observableArray([]);
	self.selectedCapacityIds = ko.observableArray([]);

	self.townArrowIcon = ko.pureComputed(function() {
		return this.showTown() ? "openArrowIcon" : "closedArrowIcon";
	}, self);
	self.facilityArrowIcon = ko.pureComputed(function() {
		return this.showFacility() ? "openArrowIcon" : "closedArrowIcon";
	}, self);
	self.activityArrowIcon = ko.pureComputed(function() {
		return this.showActivity() ? "openArrowIcon" : "closedArrowIcon";
	}, self);
	self.gearArrowIcon = ko.pureComputed(function() {
		return this.showGear() ? "openArrowIcon" : "closedArrowIcon";
	}, self);
	self.capacityArrowIcon = ko.pureComputed(function() {
		return this.showCapacity() ? "openArrowIcon" : "closedArrowIcon";
	}, self);

	self.selectedTownIds.subscribe(function(newValue) {
		let newSelectedTownIds = newValue;
		let newSelectedTowns = [];
		ko.utils.arrayForEach(newSelectedTownIds, function(townId) {
			var selectedTown = ko.utils.arrayFirst(self.towns(), function(town) {
				return (town.id === townId);
			});
			newSelectedTowns.push(selectedTown);
		});
		self.selectedTowns(newSelectedTowns);
	});
	self.selectedFacilityIds.subscribe(function(newValue) {
		let newSelectedFacilityIds = newValue;
		let newSelectedFacilities = [];
		ko.utils.arrayForEach(newSelectedFacilityIds, function(facilityId) {
			var selectedFacility = ko.utils.arrayFirst(self.facilities(), function(facility) {
				return (facility.id === facilityId);
			});
			newSelectedFacilities.push(selectedFacility);
		});
		self.selectedFacilities(newSelectedFacilities);
	});
	self.selectedActivityIds.subscribe(function(newValue) {
		let newSelectedActivityIds = newValue;
		let newSelectedActivities = [];
		ko.utils.arrayForEach(newSelectedActivityIds, function(activityId) {
			let selectedActivity = ko.utils.arrayFirst(self.activities(), function(activity) {
				return (activity.id === activityId);
			});
			newSelectedActivities.push(selectedActivity);
		});
		self.selectedActivities(newSelectedActivities);
	});
	self.selectedGearIds.subscribe(function(newValue) {
		let newSelectedGearIds = newValue;
		let newSelectedGear = [];
		ko.utils.arrayForEach(newSelectedGearIds, function(gearId) {
			let selectedGear = ko.utils.arrayFirst(self.gear(), function(gear) {
				return (gear.id === gearId);
			});
			newSelectedGear.push(selectedGear);
		});
		self.selectedGear(newSelectedGear);
	});
	self.selectedCapacityIds.subscribe(function(newValue) {
		let newSelectedCapacityIds = newValue;
		let newSelectedCapacities = [];
		ko.utils.arrayForEach(newSelectedCapacityIds, function(capacityId) {
			let selectedCapacity = ko.utils.arrayFirst(self.capacities(), function(capacities) {
				return (capacities.id === capacityId);
			});
			newSelectedCapacities.push(selectedCapacity);
		});
		self.selectedCapacities(newSelectedCapacities);
	});
};

$(document).ready(function () {
	$('.overlay').show();

	viewmodel = new ViewModel();
	ko.applyBindings(viewmodel, document.getElementById("search-page-content"));

	searchListener();
	getAutocompleteData();
	PopulateTown();
	DateTimePicker();
	getUpcomingEvents();
	$("#searchResults").hide();

	$('.overlay').hide();
});

function searchListener() {

	$('#mainSearchInput').keyup(function (e)
	{
		if (e.key === "Enter") {
			let inputValue = $('#mainSearchInput').val();

			if (inputValue !== '') {
				findSearchMethod(inputValue);
			} else {
				viewmodel.showSearchText(false);
				viewmodel.showEvents(true);
				viewmodel.items.removeAll();
				$("#searchBtn").show();
				$("#locationFilter").show();
				$("#dateFilter").show();
			}
		}
	});

	$("#searchBtn").click(function () {
		if ($('#mainSearchInput').val() !== '') {
			findSearchMethod($('#mainSearchInput').val());
		}
	});
}

function findSearchMethod(inputValue) {
	let foundResCategory = false;
	let autocompleteResObj = '';

	for(let i = 0; i < autocompleteData.length && !foundResCategory ; i++) {
		if (autocompleteData[i].label === inputValue) {
			foundResCategory = true;
			autocompleteResObj = autocompleteData[i];
		}
	}
	if (foundResCategory) {
		DoFilterSearch(autocompleteResObj);
	} else {
		doSearch(inputValue);
	}
}

function getUpcomingEvents() {
	let requestURL;
	let reqObject = {
		menuaction: "bookingfrontend.uieventsearch.upcomingEvents",
		orgID: '',
		fromDate: Util.Format.FormatDateForBackend(new Date()),
		toDate: '',
		buildingID: '',
		facilityTypeID: '',
		loggedInOrgs: '',
		start: 0,
		end: 4
	}

	requestURL = phpGWLink('bookingfrontend/', reqObject, true);

	$.ajax({
		url: requestURL,
		dataType : 'json',
		success: function (result) {
			setEventData(result);
		},
		error: function (error) {
			console.log(error);
		}
	});
}

function setEventData(result) {
	for (let i = 0; i < result.length; i++) {

		result[i].building_url = phpGWLink('bookingfrontend/', {menuaction: "bookingfrontend.uibuilding.show", id: result[i].building_id}, false);
		result[i].org_url = phpGWLink('bookingfrontend/', {menuaction: "bookingfrontend.uiorganization.show", id: result[i].org_id}, false);

		var formattedDateAndMonthArr = Util.Format.GetDateFormat(result[i].from, result[i].to);
		var eventTime = Util.Format.GetTimeFormat(result[i].from, result[i].to);

		viewmodel.events.push({
			org_id: ko.observable(result[i].org_id),
			event_name: ko.observable(result[i].event_name),
			formattedDate: ko.observable(formattedDateAndMonthArr[0]),
			monthText: ko.observable(formattedDateAndMonthArr[1]),
			event_time: ko.observable(eventTime),
			org_name: ko.observable(result[i].org_name),
			location_name: ko.observable(result[i].location_name),
			building_url: ko.observable(result[i].building_url),
			org_url: ko.observable(result[i].org_url),
			event_id: ko.observable(result[i].event_id)
		});
	}
}

function getAutocompleteData() {
	var requestURL = phpGWLink('bookingfrontend/', {menuaction: "bookingfrontend.uisearch.autocomplete_resource_and_building"}, true);

	$.getJSON(requestURL, function (result)
	{
		$.each(result, function (i, field)
		{
			autocompleteData.push({value: i, label: field.name, type: field.type, menuaction: field.menuaction, id: field.id});
		});
	}).done(function ()
	{
		$('#mainSearchInput').autocompleter({
			customValue: "value",
			source: autocompleteData,
			minLength: 1,
			highlightMatches: true,
			template: '<span>{{ label }}</span>',
			callback: function (value, index, object, event)
			{
				selectedAutocompleteValue = true;
				$('#mainSearchInput').val(autocompleteData[value].label);

				if (autocompleteData[value].type !== 'lokale') {
					window.location.href = phpGWLink('bookingfrontend/', {menuaction: autocompleteData[value].menuaction, id: autocompleteData[value].id}, false);
				}
			}
		});
	});
}

function PopulateTown() {
	let requestURL;
	let reqObject = {
		menuaction: "bookingfrontend.uisearch.get_all_towns"
	}

	requestURL = phpGWLink('bookingfrontend/', reqObject, true);

	$.ajax({
		url: requestURL,
		dataType : 'json',
		success: function (result) {
			const lowercased = result.map(res => {
				let lower = res.name.toLowerCase();
				res.name = res.name.charAt(0) + lower.slice(1);
			});

			towns = result;
			viewmodel.towns(result);
		},
		error: function (error) {
			console.log(error);
		}
	});
}

function DateTimePicker() {
	$('input[name="datefilter"]').daterangepicker({
		autoUpdateInput: false,
		autoApply: true,
		locale: {
			cancelLabel: 'Clear'
		}
	});

	$('input[name="datefilter"]').on('apply.daterangepicker', function(ev, picker) {
		const startDate = picker.startDate.format('DD.MM.YYYY');
		const endDate = picker.endDate.format('DD.MM.YYYY');

		if(startDate === endDate) {
			$(this).val(startDate);
		} else {
			$(this).val(startDate + ' - ' + endDate);
		}
	});

	$('input[name="datefilter"]').on('cancel.daterangepicker', function(ev, picker) {
		$(this).val('');
	});

	$('input[name="timefilter"]').daterangepicker({
		autoUpdateInput: false,
		timePicker : true,
		singleDatePicker:true,
		timePicker24Hour : true,
		timePickerIncrement : 15,
		locale : {
			format : 'HH:mm',
		}
	}, function (start) { //callback
		start_time = start.format('HH:mm');
		console.log(start_time);
	}).on('show.daterangepicker', function (ev, picker) {
		picker.container.find(".calendar-table").hide(); //Hide calendar
	});
}

function doSearch(searchterm_value) {
	$(".overlay").show();
	viewmodel.showEvents(false);
	viewmodel.showSearchText(true);
	$("#mainSearchInput").blur();

	//   var baseURL = document.location.origin + "/" + window.location.pathname.split('/')[1] + "/bookingfrontend/";
	const requestUrl = phpGWLink('bookingfrontend/', {menuaction: "bookingfrontend.uisearch.query", length: -1}, true);

	let searchTerm;
	if (searchterm_value !== "" && typeof searchterm_value !== "undefined")
	{
		searchTerm = searchterm_value;
	}
	else
	{
		searchTerm = $("#mainSearchInput").val();
	}

	$.ajax({
		url: requestUrl,
		type: "get",
		contentType: 'text/plain',
		data: {searchterm: searchTerm},
		success: function (response)
		{

			console.log(response);

			viewmodel.items.removeAll();
			for (var i = 0; i < response.results.results.length; i++)
			{
				var url = "";
				if (response.results.results[i].type === "building")
				{
					url = phpGWLink('bookingfrontend/', {menuaction: "bookingfrontend.uibuilding.show", id: response.results.results[i].id}, false);

				}
				else if (response.results.results[i].type === "resource")
				{
					if (response.results.results[i].simple_booking === 1)
					{
						url = phpGWLink('bookingfrontend/', {menuaction: "bookingfrontend.uiapplication.add", resource_id: response.results.results[i].id,
							building_id: response.results.results[i].building_id, simple: 1}, false);
					}
					else
					{
						url = phpGWLink('bookingfrontend/', {menuaction: "bookingfrontend.uiresource.show", id: response.results.results[i].id,
							building_id: response.results.results[i].building_id}, false);
					}
				}
				else if (response.results.results[i].type === "organization")
				{
					url = phpGWLink('bookingfrontend/', {menuaction: "bookingfrontend.uiorganization.show", id: response.results.results[i].id}, false);
				}
				viewmodel.items.push({name: response.results.results[i].name,
					street: typeof response.results.results[i].street === "undefined" ? "" : response.results.results[i].street,
					postcode: (typeof response.results.results[i].zip_code === "undefined" ? "" : response.results.results[i].zip_code) + " " +
						typeof response.results.results[i].city === "undefined" ? "" : response.results.results[i].city,
					activity_name: response.results.results[i].activity_name,
					itemLink: url,
					resultType: GetTypeName(response.results.results[i].type).toUpperCase(),
					type: response.results.results[i].type,
					tagItems: []
				});
			}
			setTimeout(function ()
			{
				$('html, body').animate({
					scrollTop: $("#searchResult").offset().top - 100
				}, 1000);
			}, 800);

			$(".overlay").hide();

		},
		error: function (xhr)
		{
		}
	});
}

function DoFilterSearch(resCategory, town = '', from = '', to = '')
{
	let lokale;
	let townId = '';
	let datoFra, datoTil = '';

	lokale = resCategory;

	console.log($('#locationFilter').val());

	if ($('#locationFilter').val() !== '') {
		town = $('#locationFilter').val();

		let foundTown = false;

		for(let i = 0; i < towns.length && !foundTown ; i++) {
			console.log(`${town} ${towns[i].name}`)
			if (towns[i].name === town) {
				foundTown = true;
				townId = towns[i].id;
			}
		}
		if (!foundTown) {
			townId = '';
		}
		towns.push(townId);
	}

	if ($('#dateFilter').val() !== '' && !(/[a-zA-Z]/g).test($('#dateFilter').val())) {
		let date = $('#dateFilter').val();

		if (date.includes("-")) {
			from = date.substr(0,8)
			to = date.substr(11, 18);
		}
		else {
			from = date.substr(0, 8);
		}
	}

	var requestURL = phpGWLink('bookingfrontend/', {menuaction: "bookingfrontend.uisearch.resquery", length: -1}, true);

	$.ajax({
		url: requestURL,
		type: "get",
		contentType: 'text/plain',
		data: {
			rescategory_id: resCategory.id,
			part_of_town_id: townId
		},
		success: function (response)
		{
			$("#mainSearchInput").blur();
			$("#locationFilter").hide();
			$("#dateFilter").hide();
			$("#searchBtn").hide();
			$("#searchResults").show();
			viewmodel.showEvents(false);
			viewmodel.resources.removeAll();
			viewmodel.towns.removeAll();
			viewmodel.facilities.removeAll();
			viewmodel.activities.removeAll();
			viewmodel.gear.removeAll();
			viewmodel.capacities.removeAll();

			$('#dateFilter2').val($('#dateFilter').val());

			console.log(response);

			for (let i = 0; i < response.buildings.length; i++) {
				for (let j = 0; j < response.buildings[i].resources.length; j++) {
					viewmodel.resources.push({
						name: response.buildings[i].resources[j].name,
						id: response.buildings[i].resources[j].id,
						location: typeof response.buildings[i].city === "undefined" ? response.buildings[i].name : response.buildings[i].name + ' - ' + response.buildings[i].city
					})
				}
			}

			setTownData(response.partoftowns);
			setFacilityData(response.facilities);
			setActivityData(response.activities);


		},
		error: function (e) {
			console.log(e);
		}
	});
}

function setTownData(towns) {
	if (towns.length !== 0) {
		const lowercased = towns.map(res => {
			let lower = res.name.toLowerCase();
			res.name = res.name.charAt(0) + lower.slice(1);
		});

		for (let i = 0; i < lowercased.length; i++) {
			viewmodel.towns.push({
				name: towns[i].name,
				id: towns[i].id
			});
		}
	} else {
		viewmodel.showTown(false);
	}
}

function setFacilityData(facilities) {
	if (facilities.length !== 0) {
		for (let i = 0; i < facilities.length; i++) {
			viewmodel.facilities.push({
				name: facilities[i].name,
				id: facilities[i].id
			});
		}
	} else {
		viewmodel.showFacility(false);
	}
}

function setActivityData(activities) {
	if (activities.length !== 0) {
		for (let i = 0; i < activities.length; i++) {
			viewmodel.activities.push({
				name: activities[i].name,
				id: activities[i].id
			});
		}
	} else {
		viewmodel.showActivity(false);
	}
}

	/*$.getJSON(requestURL, function (result)
	{
		console.log(result);

		for (var i = 0; i < result.facilities.length; i++)
		{
			var selectedFacilities = false;
			var alreadySelected = ko.utils.arrayFirst(searchViewModel.selectedFacilities(), function (current)
			{
				return current == result.facilities[i].id;
			});
			if (alreadySelected)
			{
				selectedFacilities = true;
			}
			viewmodel.facilities.push(ko.observable({index: i, facilityOption: result.facilities[i].name,
				facilityOptionId: result.facilities[i].id,
				facilitySelected: "facilitySelected",
				selected: ko.observable(selectedFacilities)}));
		}

		for (var i = 0; i < result.activities.length; i++)
		{
			viewmodel.activities.push({activityOption: result.activities[i].name,
				activityOptionId: result.activities[i].id,
				activitySelected: "activitySelected"});
		}

		for (var i = 0; i < result.partoftowns.length; i++)
		{
			var selectedTown = false;
			var alreadySelected = ko.utils.arrayFirst(viewmodel.selectedTowns(), function (current)
			{
				return current == result.partoftowns[i].id;
			});
			if (alreadySelected)
			{
				selectedTown = true;
			}

			viewmodel.towns.push({townOption: result.partoftowns[i].name,
				townOptionId: result.partoftowns[i].id,
				townSelected: "townSelected",
				selected: ko.observable(selectedTown)});
		}

		var items = [];
		for (var i = 0; i < result.buildings.length; i++)
		{
			var resources = [];
			for (var k = 0; k < result.buildings[i].resources.length; k++)
			{
				var bookBtnURL;
				if (result.buildings[i].resources[k].simple_booking == 1)
				{
					bookBtnURL = phpGWLink('bookingfrontend/', {
						menuaction: "bookingfrontend.uiapplication.add",
						resource_id: result.buildings[i].resources[k].id,
						building_id: result.buildings[i].id,
						simple: 1
					}, false);

				}
				else
				{
					bookBtnURL = phpGWLink('bookingfrontend/', {
						menuaction: "bookingfrontend.uiresource.show",
						id: result.buildings[i].resources[k].id,
						building_id: result.buildings[i].id
					}, false);
				}

				var facilities = [];
				var activities = [];
				for (var f = 0; f < result.buildings[i].resources[k].facilities_list.length; f++)
				{
					facilities.push({name: result.buildings[i].resources[k].facilities_list[f].name});
				}
				for (var f = 0; f < result.buildings[i].resources[k].activities_list.length; f++)
				{
					activities.push({name: result.buildings[i].resources[k].activities_list[f].name});
				}
				resources.push({name: result.buildings[i].resources[k].name, forwardToApplicationPage: bookBtnURL, id: result.buildings[i].resources[k].id, facilities: facilities, activities: activities, limit: result.buildings[i].resources.length > 1 ? true : false});

			}
			items.push({resultType: GetTypeName("building").toUpperCase(),
				name: result.buildings[i].name,
				street: result.buildings[i].street,
				postcode: result.buildings[i].zip_code + " " + result.buildings[i].city,
				filterSearchItemsResources: ko.observableArray(resources),
				itemLink: phpGWLink('bookingfrontend/', {menuaction: "bookingfrontend.uibuilding.show", id: result.buildings[i].id}, false)});
		}
		console.log(viewmodel.facilities);
		console.log(viewmodel.towns);
		console.log(viewmodel.activities);

		viewmodel.filterSearchItems(items);


	});
}
	 */


function GetTypeName(type)
{
	if (type.toLowerCase() == "building")
	{
		return "anlegg";
	}
	else if (type.toLowerCase() == "resource")
	{
		return "lokale";
	}
	else if (type.toLowerCase() == "organization")
	{
		return "org";
	}
}
