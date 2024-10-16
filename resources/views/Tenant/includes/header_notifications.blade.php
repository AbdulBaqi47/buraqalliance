<script type="text/javascript">
    $(function() {
        $.ajax({
            url: "{{route('tenant.admin.appconfig')}}",
            type: 'POST',
            contentType: false,
            cache: false,
            headers: {
                'X-NOFETCH': ''
            },
            processData: false
        })
        .done(function (response) {

            // -------------------------
            // Renders cheques
            // -------------------------
            if (response.pending_cheques.data.length > 0) {
                let all_data = {}
                response.pending_cheques.all_accounts.forEach(account => {
                    all_data[account] = response.pending_cheques.data.filter(item => {
                        return item.account === account;
                    })
                });
                let header_pending_cheques = $('#appconfig_header_pending_cheques')
                header_pending_cheques.attr('hidden', false);
                let html = '';
                Object.keys(all_data).forEach(key => {
                    let total_amount = all_data[key].reduce((prev, current) => (prev + current.amount), 0)
                    let dates = all_data[key].map(current => moment(current.additional_details.charge_date));
                    let smallest_date = moment.min(dates);
                    let difference_in_days = smallest_date.diff(moment(), 'days')
                    let is_diff_negative = difference_in_days < 0;
                    difference_in_days = Math.abs(difference_in_days)
                    html += `
                    <div>
                        <a href="{{route('accounts.transaction.pending')}}" style="color: yellow; font-size: 12px;">AED ${(total_amount)} Payable from <span class="kt-font-bold">${key} - </span> <span class="${is_diff_negative ? 'kt-font-danger kt-font-bold': ''}">${is_diff_negative ? 'Overdue ': 'Due in '} ${difference_in_days == 1 ? difference_in_days + ' Day': difference_in_days + ' Days'}</span>
                        </a>
                        <button type="button" style="height: 1.5rem;width: 1.5rem;" class="btn btn-outline-info btn-elevate btn-icon btn-sm btn-circle cheque_account_detail" data-toggle="header_popover" data-trigger="focus" data-placement="bottom" data-html="true"
                        data-content="
                        <ul class='list-group'>
                            ${all_data[key].map(item => {
                                return `
                                <li class='list-group-item'>
                                    <div class='d-flex flex-column'>

                                        <div>
                                            <span class='kt-font-bolder'>Amount:</span>
                                            <span>AED ${item.amount}</span>
                                            ${!!item.additional_details && item.additional_details.is_guarantee ? `<small class='text-success ml-5 font'>Guarantee</small>` : ''}
                                        </div>

                                        ${typeof item.additional_details !== "undefined" ? `

                                            ${typeof item.additional_details.charge_date !== "undefined" && !!item.additional_details.charge_date ? `
                                                <div>
                                                    <span class='kt-font-bolder'>Due Date:</span>
                                                    <span>${moment(item.additional_details.charge_date).format('DD/MMM/YYYY')}</span>
                                                </div>
                                            ` : ''}

                                            ${typeof item.additional_details.cheque_number !== "undefined" && !!item.additional_details.cheque_number ? `
                                                <div>
                                                    <span class='kt-font-bolder'>Cheque #:</span>
                                                    <span>${item.additional_details.cheque_number}</span>
                                                </div>
                                            ` : ''}

                                            ${typeof item.additional_details.cheque_beneficiary !== "undefined" && !!item.additional_details.cheque_beneficiary ? `
                                                <div>
                                                    <span class='kt-font-bolder'>Cheque Beneficiary:</span>
                                                    <span>${item.additional_details.cheque_beneficiary}</span>
                                                </div>
                                            ` : ''}

                                        ` : ''}

                                    </div>
                                </li>`;
                            }).join('')}
                        </ul>
                        " data-original-title="" title="">
                            <i class="la la-question"></i>
                        </button>
                    </div>`;
                })
                header_pending_cheques.html(html);
                $('[data-toggle="header_popover"]').popover();
            }

            // -------------------------
            // Render Expiring Visa Data
            // -------------------------
            if (response.expiring_visa.length > 0) {
                // Show Available Notifications Dot
                $('.kt-header-notification-visa').attr('hidden', false);
                $('.kt-header-notification-visa .kt-header__topbar-icon span').show();
                // Sets Notifications Counter
                $('.kt-header-notification-visa .kt-head__desc').text(`${response.expiring_visa.length} new notification${(response.expiring_visa.length !== 1) ? 's' : ''}`);
                let html = response.expiring_visa.map(item => {
                    return `<span class="kt-notification__item">
                        <div class="kt-notification__item-details">
                            <div class="kt-notification__item-title">
                                <a href="{{route('tenant.admin.drivers.viewDetails','__param')}}"> ${item.name}</a><br/> Expires ${moment(item.expiry).fromNow()}
                            </div>
                            <div class="kt-notification__item-time">
                                <a href="{{route('tenant.admin.vehicleledger.booking.view','__:param')}}"> B#${item.booking_id}</a>
                            </div>
                        </div>
                    </span>`.replace('__param', item.id).replace('__:param', item.booking_id)
                })
                html += `
                <div class="ps__rail-x" style="left: 0px; bottom: 0px;">
                    <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
                </div>
                <div class="ps__rail-y" style="top: 0px; right: 0px;">
                    <div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 0px;"></div>
                </div>`;
                $('.kt-header-notification-visa .kt-header__topbar-icon').attr('title', `Visa`)
                $('.kt-header-notification-visa .kt-header__topbar-icon .noti_circle').text(response.expiring_visa.length)
                $('.kt-header-notification-visa .kt-notification').html(html)
            }

            // -------------------------
            // Render Expiring License Data
            // -------------------------
            if (response.expiring_liscense.length > 0) {
                // Show Available Notifications Dot
                $('.kt-header-notification-liscense').attr('hidden', false);
                $('.kt-header-notification-liscense .kt-header__topbar-icon span').show();
                // Sets Notifications Counter
                $('.kt-header-notification-liscense .kt-head__desc').text(`${response.expiring_liscense.length} new notification${(response.expiring_liscense.length !== 1) ? 's' : ''}`);
                let html = response.expiring_liscense.map(item => {
                    return `<span class="kt-notification__item">
                        <div class="kt-notification__item-details">
                            <div class="kt-notification__item-title">
                                <a href="{{route('tenant.admin.drivers.viewDetails','__param')}}"> ${item.name}</a><br/> Expires ${moment(item.expiry).fromNow()}
                            </div>
                            <div class="kt-notification__item-time">
                                <a href="{{route('tenant.admin.vehicleledger.booking.view','__:param')}}"> B#${item.booking_id}</a>
                            </div>
                        </div>
                    </span>`.replace('__param', item.id).replace('__:param', item.booking_id)
                })
                html += `
                <div class="ps__rail-x" style="left: 0px; bottom: 0px;">
                    <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
                </div>
                <div class="ps__rail-y" style="top: 0px; right: 0px;">
                    <div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 0px;"></div>
                </div>`;
                $('.kt-header-notification-liscense .kt-header__topbar-icon').attr('title', `Liscence`)
                $('.kt-header-notification-liscense .kt-header__topbar-icon .noti_circle').text(response.expiring_liscense.length)
                $('.kt-header-notification-liscense .kt-notification').html(html)
            }

            // -------------------------
            // Render Expiring RTA Data
            // -------------------------
            if (response.expiring_rta.length > 0) {
                // Show Available Notifications Dot
                $('.kt-header-notification-rta').attr('hidden', false);
                $('.kt-header-notification-rta .kt-header__topbar-icon span').show();
                // Sets Notifications Counter
                $('.kt-header-notification-rta .kt-head__desc').text(`${response.expiring_rta.length} new notification${(response.expiring_rta.length !== 1) ? 's' : ''}`);
                let html = response.expiring_rta.map(item => {
                    return `<span class="kt-notification__item">
                        <div class="kt-notification__item-details">
                            <div class="kt-notification__item-title">
                                <a href="{{route('tenant.admin.drivers.viewDetails','__param')}}"> ${item.name}</a><br/> Expires ${moment(item.expiry).fromNow()}
                            </div>
                            <div class="kt-notification__item-time">
                                <a href="{{route('tenant.admin.vehicleledger.booking.view','__:param')}}"> B#${item.booking_id}</a>
                            </div>
                        </div>
                    </span>`.replace('__param', item.id).replace('__:param', item.booking_id)
                })
                html += `
                <div class="ps__rail-x" style="left: 0px; bottom: 0px;">
                    <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
                </div>
                <div class="ps__rail-y" style="top: 0px; right: 0px;">
                    <div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 0px;"></div>
                </div>`;
                $('.kt-header-notification-rta .kt-header__topbar-icon').attr('title', `RTA`)
                $('.kt-header-notification-rta .kt-header__topbar-icon .noti_circle').text(response.expiring_rta.length)
                $('.kt-header-notification-rta .kt-notification').html(html)
            }
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
            // since this is gonna be silent requent, we cannot show any popups
            console.error("Error While fetching app settings", jqXHR);
        });
    })
</script>
