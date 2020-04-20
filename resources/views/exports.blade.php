@extends('layouts.app')
@section('content')


<div class="container">
    <div class="search-container row card">
        <div class="col-sm-12 col-md-12 col-lg-12" style="margin-top:5px;">
            <h2>Exports</h2>
            <hr>
            <div>Welcome to the exports page. Here you will find links to download .csv files that will contain data that can be used for analytics and targeted advertising purposes. Some of this data has been anonymised for the safety of our users. Currently we offer 5 types of data for download: search data, geographical search data, booking data, personal data and viewing data.</div>
            <br>
            <br>
            <hr>
            <div style="text-align:center;">
                <h3>Search Data</h3>
                <a class="btn btn-primary" id="download_search" href="/download_search_data" style="margin-bottom:5px;"><i class="fas fa-file-upload"></i>&nbsp;Search Data</a>
                <p class="text-muted">Our search data is structued as so: age (int), gender (M,F,X),  searched_at (epoch timestamp), search_name (string), search_address (string), search_suburb (~ separated string of suburbs), search_tags (~ separated string of property tags), search_minimum_rating (int [1-5]), search_unrated (boolean), search_beds (int), search_baths (int), search_cars (int), search_start_date (epoch timestamp for the start date of a potential booking), search_end_date (epoch timestamp for the end date of a potential booking) </p>
            </div>
            <hr>
            <div style="text-align:center;">
                <h3>Geographical Search Data</h3>
                <a class="btn btn-primary" id="download_geo" href="/download_georgraphical_search_data" style="margin-bottom:5px;"><i class="fas fa-file-upload"></i>&nbsp;Georgraphical Search Data</a>
                <p class="text-muted">Our geographical search data is structured as so: age (int), gender (M,F,X), searched_at (epoch timestamp), search_lattitude (lattitude of the center of the search area), search_longitude (longitude of the center of the search area), search_radius (int; radius of search area in meters)</p>
            </div>
            <hr>
            <div style="text-align:center;">
                <h3>Booking Data</h3>
                <a class="btn btn-primary" id="download_geo" href="/download_booking_data" style="margin-bottom:5px;"><i class="fas fa-file-upload"></i>&nbsp;Booking Data</a>
                <p class="text-muted">Our booking data is structured as so: age (int), gender (M,F,X), booking_start_date (epoch timestamp), booking_end_date (epoch timestamp), booking_persons (int), booking_suburb (string), booking_tags (~ separated string of property tags), booking_beds (int), booking_baths (int), booking_cars (int), booking_keyword (string of the property's advertised title)</p>
            </div>
            <hr>
            <div style="text-align:center;">
                <h3>Personal Data</h3>
                <a class="btn btn-primary" id="download_geo" href="/download_personal_data" style="margin-bottom:5px;"><i class="fas fa-file-upload"></i>&nbsp;Personal Data</a>
                <p class="text-muted">Our personal data is structured as so: age (int), gender (M,F,X), name (string), phone (string), email (email address)</p>
            </div>
            <hr>
            <div style="text-align:center;">
                <h3>Viewing Data</h3>
                <a class="btn btn-primary" id="download_geo" href="/download_viewing_data" style="margin-bottom:5px;"><i class="fas fa-file-upload"></i>&nbsp;Viewing Data</a>
                <p class="text-muted">Our viewing data is structured as so: age (int), gender (M,F,X), viewed_at (epoch timestamp), property_suburb, property_tags (~ separated string of property tags), property_title (string), property_beds (int), property_baths (int), property_cars (int)</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {

});
</script>
@endsection
 