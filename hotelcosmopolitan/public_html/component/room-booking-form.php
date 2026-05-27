<div class="sc-hb-rooms-search style-01">
    <div class="hotel-booking-search style-01">
        <h3>Check Room Availability</h3>
        <form action="#" class="hb-search-form" method="get">
            <ul class="hb-form-table">
                <li><input type="text" id="multidate" class="multidate" value="" data-date-min="6"></li>
                <li class="hb-form-field hb-form-check-in">
                    <div class="label">Check-In</div>
                    <div class="hb-form-field-input hb_input_field">
                        <input type="text" id="day" class="day" value="">
                        <input id="month" class="month" type="text" value="">
                        <input type="hidden" name="check_in_date" id="check_in_date" class="check-date hasDatepicker" value="">
                    </div>
                </li>
                <li class="hb-form-field hb-form-check-out">
                    <div class="label">Check-Out</div>
                    <div class="hb-form-field-input hb_input_field">
                        <input type="text" id="day2" class="day" value="">
                        <input id="month2" class="month" type="text" value="">
                        <input type="hidden" name="check_out_date" id="check_out_date" class="check-date hasDatepicker" value="">
                    </div>
                </li>
                <li class="hb-form-field hb-form-number">
                    <div class="label">Guest</div>
                    <div id="" class="hb-form-field-input hb_input_field">
                        <input type="number" id="number" class="day" value="1" min="1" max="100">
                        <input class="month" type="text" value="Adults">
                    </div>
                </li>
                <li class="hb-form-field hb-form-number">
                    <div class="label">Rooms</div>
                    <div id="rooms" class="hb-form-field-input hb_input_field">
                        <input type="number" id="roomNumber" class="day" value="1" min="1" max="50">
                        <input class="month" type="text" value="Rooms">
                    </div>
                </li>


                <div class="daterangepicker dropdown-menu ltr show-calendar opensright">
                    <div class="calendar left">
                        <div class="daterangepicker_input"><input class="input-mini form-control" type="text" name="daterangepicker_start" value=""><i class="fa fa-calendar glyphicon glyphicon-calendar"></i>
                            <div class="calendar-time" style="display: none;">
                                <div></div><i class="fa fa-clock-o glyphicon glyphicon-time"></i>
                            </div>
                        </div>
                        <div class="calendar-table"></div>
                    </div>
                    <div class="calendar right">
                        <div class="daterangepicker_input"><input class="input-mini form-control" type="text" name="daterangepicker_end" value=""><i class="fa fa-calendar glyphicon glyphicon-calendar"></i>
                            <div class="calendar-time" style="display: none;">
                                <div></div><i class="fa fa-clock-o glyphicon glyphicon-time"></i>
                            </div>
                        </div>
                        <div class="calendar-table"></div>
                    </div>
                    <div class="ranges" style="display: none;">
                        <div class="range_inputs"><button class="applyBtn btn btn-sm btn-success" disabled="disabled" type="button">Apply</button> <button class="cancelBtn btn btn-sm btn-default" type="button">Cancel</button></div>
                    </div>
                </div>
            </ul>
            <p class="hb-submit">
                <span class="contact-info">Need Help: <span><a href="tel:+917966016601">+917966016601</a></span></span>
                <button type="submit">Check Availability</button>
            </p>
        </form>
    </div>
</div>