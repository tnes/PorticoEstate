<!-- $Id$ -->
<xsl:template match="data"  xmlns:php="http://php.net/xsl">
<xsl:variable name="date_format">d/m-Y</xsl:variable>
	
<script>
<xsl:text>

$(document).ready(function(){

	var oArgs = {menuaction:'property.bolocation.get_locations_by_name'};
	var baseUrl = phpGWLink('index.php', oArgs, false);

	var location_type = $("#loc_type").val();

	$("#search-location-name").autocomplete({
		source: function( request, response ) {
			location_type = $("#loc_type").val();
		
			$.ajax({
				url: baseUrl,
				dataType: "json",
				data: {
					location_name: request.term,
					level: location_type,
					phpgw_return_as: "json"
				},
				success: function( data ) {
					response( $.map( data, function( item ) {
						return {
							label: item.name,
							value: item.location_code
						}
					}));
				}
			});
		},
		focus: function (event, ui) {
 			$(event.target).val(ui.item.label);
  			return false;
		},
		minLength: 1,
		select: function( event, ui ) {
		  chooseLocation( ui.item.label, ui.item.value);
		}
	});
});

function chooseLocation( label, value ){
	var currentYear = $("#currentYear").val();
	
	var oArgs = {menuaction:'controller.uicalendar.view_calendar_for_year'};
	var baseUrl = phpGWLink('index.php', oArgs, false);
	var requestUrl = baseUrl +  "&amp;location_code=" + value + "&amp;year=" + currentYear;
	
	window.location.replace(requestUrl);
}

</xsl:text>

</script>

<div id="main_content">

	<div id="control_plan">
		<div class="top">
			<xsl:choose>
				<xsl:when test="location_level = 1">
					<h1>Kontrollplan for eiendom: <xsl:value-of select="current_location/loc1_name"/></h1>
				</xsl:when>
				<xsl:otherwise>
						<h1>Kontrollplan for bygg: <xsl:value-of select="current_location/loc2_name"/></h1>
				</xsl:otherwise>
			</xsl:choose>
			
			<h3>Kalenderoversikt for <span class="year"><xsl:value-of select="current_year"/></span></h3>
			
			<!-- =====================  SEARCH FOR LOCATION  ================= -->
			<div id="search-location" class="select-box">
				<div id="choose_loc">
					<label>Søk etter andre <a href="loc_type_2" class="btn active">Bygg</a><a href="loc_type_1" class="btn">Eiendom</a>
							<input id="loc_type" type="hidden" name="loc_type" value="2" />
					</label>
				</div>
				<input type="hidden" id="currentYear">
					<xsl:attribute name="value">
						<xsl:value-of select="current_year"/>
					</xsl:attribute>
				</input>
				<input type="text" value="" id="search-location-name" />
			</div>
			
			<!-- =====================  SELECT LIST FOR MY LOCATIONS  ================= -->
			<div id="choose-my-location" class="select-box">
				<label>Velg et annet bygg du har ansvar for</label>
				<xsl:call-template name="select_my_locations" />
			</div>
		</div>
		
		<div class="middle">
		
			<!-- =====================  CHOOSE ANOTHER BUILDING ON PROPERTY  ================= -->
			<div id="choose-building" class="select-box">
				<xsl:if test="location_level > 1">
					<a>
						<xsl:attribute name="href">
							<xsl:text>index.php?menuaction=controller.uicalendar.view_calendar_for_year</xsl:text>
							<xsl:text>&amp;year=</xsl:text>
							<xsl:value-of select="current_year"/>
							<xsl:text>&amp;location_code=</xsl:text>
							<xsl:value-of select="current_location/loc1"/>
						</xsl:attribute>
						Vis kontrollplan for eiendom
					</a> 
				</xsl:if>

				<label>Velg et annet bygg på eiendommen</label>
				<xsl:call-template name="select_buildings_on_property" />
			</div>
			
			<!-- 
 				<select id="loc_1" class="choose_loc">
					<xsl:for-each select="property_array">
						<xsl:variable name="loc_code"><xsl:value-of select="location_code"/></xsl:variable>
						<xsl:choose>
							<xsl:when test="location_code = current_location/location_code">
								<option value="{$loc_code}" selected="selected">
									<xsl:value-of disable-output-escaping="yes" select="loc1_name"/>
								</option>
							</xsl:when>
							<xsl:otherwise>
								<option value="{$loc_code}">
									<xsl:value-of disable-output-escaping="yes" select="loc1_name"/>
								</option>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:for-each>
				</select>				
			-->
			
			<!-- =====================  COLOR ICON MAP  ================= -->
			<xsl:call-template name="icon_color_map" />
			
			<!-- =====================  CALENDAR NAVIGATION  ================= -->
			<div id="calNav">
				<a class="showPrev">
					<xsl:attribute name="href">
						<xsl:text>index.php?menuaction=controller.uicalendar.view_calendar_for_year</xsl:text>
						<xsl:text>&amp;year=</xsl:text>
						<xsl:value-of select="current_year - 1"/>
						<xsl:text>&amp;location_code=</xsl:text>
						<xsl:value-of select="current_location/location_code"/>
					</xsl:attribute>
					<img height="17" src="controller/images/left_arrow_simple_light_blue.png" />
					<xsl:value-of select="current_year - 1"/>
				</a>
				<span class="current">
						<xsl:value-of select="current_year"/>
				</span>
				<a class="showNext">
						<xsl:attribute name="href">
						<xsl:text>index.php?menuaction=controller.uicalendar.view_calendar_for_year</xsl:text>
						<xsl:text>&amp;year=</xsl:text>
						<xsl:value-of select="current_year + 1"/>
						<xsl:text>&amp;location_code=</xsl:text>
						<xsl:value-of select="current_location/location_code"/>
					</xsl:attribute>
					<xsl:value-of select="current_year + 1"/>
					<img height="17" src="controller/images/right_arrow_simple_light_blue.png" />
				</a>
			</div>
			
		</div>
		 
		<div id="cal_wrp">
		<table id="calendar">
				<tr class="heading">
						<th class="title"><span>Tittel</span></th>
						<th class="assigned"><span>Tildelt</span></th>
						<th class="frequency"><span>Frekvens</span></th>
					<xsl:for-each select="heading_array">
						<th>
							<a>
								<xsl:attribute name="href">
									<xsl:text>index.php?menuaction=controller.uicalendar.view_calendar_for_month</xsl:text>
									<xsl:text>&amp;year=</xsl:text>
									<xsl:value-of select="//current_year"/>
									<xsl:text>&amp;location_code=</xsl:text>
									<xsl:value-of select="current_location/location_code"/>
									<xsl:text>&amp;month=</xsl:text>
									<xsl:number/>
								</xsl:attribute>
								
								<xsl:variable name="month_str">short_month <xsl:number/> capitalized</xsl:variable>
								<xsl:value-of select="php:function('lang', $month_str)" />
							</a>				
						</th>
					</xsl:for-each>
				</tr>
			
			<xsl:choose>
				<xsl:when test="controls_calendar_array/child::node()">
				
			  	<xsl:for-each select="controls_calendar_array">
			  		<xsl:variable name="control_id"><xsl:value-of select="control/id"/></xsl:variable>
			  	
			  		<tr>				
						<xsl:choose>
					        <xsl:when test="(position() mod 2) != 1">
					            <xsl:attribute name="class">odd</xsl:attribute>
					        </xsl:when>
					        <xsl:otherwise>
					            <xsl:attribute name="class">even</xsl:attribute>
					        </xsl:otherwise>
					    </xsl:choose>
							<td class="title">
				      			<span><xsl:value-of select="control/title"/></span>
							</td>
							<td class="assigned">
				      			<span><xsl:value-of select="control/responsibility_name"/></span>
							</td>
							<td class="frequency">
				      			<span>
					      			<xsl:choose>
					      				<xsl:when test="control/repeat_interval = 1">
					      					<span class="pre">Hver</span>
					      				</xsl:when>
					      				<xsl:when test="control/repeat_interval = 2">
					      					<span class="pre">Annenhver</span>
					      				</xsl:when>
					      				<xsl:when test="control/repeat_interval > 2">
					      					<span class="pre">Hver</span><span><xsl:value-of select="control/repeat_interval"/>.</span>
					      				</xsl:when>
					      			</xsl:choose>
					      			
					      			<span class="val"><xsl:value-of select="control/repeat_type_label"/></span>
				      			</span>
							</td>
							<xsl:for-each select="calendar_array">
								<xsl:call-template name="check_list_status_checker" >
									<xsl:with-param name="location_code"><xsl:value-of select="//current_location/location_code"/></xsl:with-param>
								</xsl:call-template>
							</xsl:for-each>
					</tr>	
				</xsl:for-each>	
			</xsl:when>
			<xsl:otherwise>
				<tr class="cal_info_msg"><td colspan="3">Ingen sjekklister for bygg i angitt periode</td></tr>
			</xsl:otherwise>
		</xsl:choose>
	</table>
	</div>
</div>
</div>
</xsl:template>
