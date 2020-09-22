<?php
    phpgw::import_class("booking.uicommon");
    phpgw::import_class('bookingfrontend.bosearch');

    class bookingfrontend_uieventsearch extends booking_uicommon
    {

        //fields
        protected $module;
        protected $bosearch;

        public function __construct()
        {
            parent::__construct();
            $this->module= "bookingfrontend";
            $this->bosearch = new bookingfrontend_bosearch();
        }


        public $public_functions = array
        (
            'index' => true,
            'show'  => true,
            'upcomingEvents' => true
        );

        public function show()
        {
            phpgwapi_jquery::load_widget('autocomplete');

            echo "hellos";

            $event_search['dickens']="test";
            $config = CreateObject('phpgwapi.config', 'booking');
            $config->read();
            _debug_array($event_search);
            phpgwapi_jquery::load_widget("core");

            self::add_javascript('bookingfrontend', 'base', 'event_search.js', 'text/javascript', true);
            self::render_template_xsl('event_search', array('event_search' => $event_search));

        }

        /***
         * Metode for å hente events til søkesiden
         */
        public function upcomingEvents()
        {
            $currentDate = date('Y-m-d H:i:s');
            echo $currentDate;
            $this->bosearch->soevent->get_events_from_date();
        }

        public function query()
        {
            // TODO: Implement query() method.
        }
        public function index()
        {
            if (phpgw::get_var('phpgw_return_as') == 'json')
            {
                return $this->query();
            }

            phpgw::no_access();
        }

}