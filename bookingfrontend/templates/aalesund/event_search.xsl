<xsl:template match="data" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <!--    <script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>-->
    <div id="container_event_search col" style="flex-direction:column">
        <div class="col my_orgs"><button onclick="toggleMyOrgs()" class="fa fa-circle" id="my_orgs_button" style="display='none';">Vis mine arrangement</button></div>
        <div class="container searchContainer" style="flex-direction:column">
            <div class="input-group input-group-lg mainpageserchcontainer" style="flex-wrap:inherit">
                <input type="text" class="eventsearchbox" id="field_org_name" aria-label="Large" placeholder="søk etter organisasjoner"/>
                <input id="field_org_id" name="organization_id" type="hidden"/>
                <div class="input-group-prepend">
                    <button class="input-group-text searchBtn" id="inputGroup-sizing-lg" type="button" onclick="searchInput()">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                <div id="org_container"></div>
            </div>
            <div class="row datepicker">
                <div class="col">
                    <label for="from">From</label>
                    <input type="date" id="from" name="from"/>
                </div>
                <div class="col">
                    <label for="to">To</label>
                    <input type="date" id="to" name="to"/>
                </div>
            </div>
            <div class="row filterboxcontainer" id="filterboxcontainer">
                <button onclick="clearFilters()" class="fa fa-times" aria-hidden="true">Fjern Filter</button>
                <div class="dropdown col">
                    <button onclick="buildingNameDropDown()" class="dropbtn" id="dropBuildingNameButton">Bygnings navn</button>
                    <div id="buildingNameDropDown" class="dropdown-content">
                        <input type="text" placeholder="Search.." id="field_building_name"/>
                        <input type="hidden" id="field_building_id"/>
                        <div class="dropdown_list_container" id="building_container"></div>
                    </div>
                </div>
                <div class="buildingTypeDropdown col">
                    <button onclick="buildingTypeDropDown()" class="dropbtn" id="dropBuildingTypeButton">Bygnings type</button>
                    <div id="buildingTypeDropDown" class="dropdown-content">
                        <input type="text" placeholder="Search.." id="field_type_name"/>
                        <input type="hidden" id="field_type_id"/>
                        <div class="dropdown_list_container" id="buildingtype_container"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="event-content" class="col">
        <h2 class="Kommende-arrangement">Kommende Arrangement</h2>
        <ul data-bind="foreach: events">
            <div class="event-card">
                <li>
                    <div class="card-element-left">
                        <div class="formattedDate-container">
                            <div class="cal-img-logo"></div>

                            <span class="formattedDate"  data-bind="text: formattedDate"></span>
                            <span class="monthTag" data-bind="text:monthText"></span>

                        </div>
                    </div>
                    <div class="card-element-mid">
                        <div class="event_name-container">
                            <span class="event_name" data-bind="text: event_name"></span>
                        </div>
                        <div class="event_time-container">
                            <span class="event_time" data-bind="text: event_time"></span>
                        </div>
                    </div>
                    <div class="card-element-right">
                        <div class="location_container" >
                            <div class="pin_img_logo"></div>
                            <a href="#" data-bind="click:$parent.goToBuilding">
                                <span class="location_name" data-bind="text: location_name"></span>
                            </a>
                        </div>
                        <div class ="org_name-container">
                            <div class="fas fa-users"></div>
                            <a href="#" data-bind="click:$parent.goToOrganization">
                                <span class="org_name" data-bind="text: org_name"></span>
                            </a>
                        </div>
                    </div>
                </li>
            </div>
        </ul>
    </div>
</xsl:template>