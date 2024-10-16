@php
    $negativeAccess = $helper_service->helper->negativeBalanceData();
@endphp

<script>

var KR_ACCOUNTS_CONFIG={
    accountSelectorClassList:'kr-select2',
    eachAccountSelectorCallback:function(selectElem){
        if(typeof kingriders !== "undefined")kingriders.Plugins.update_select2(selectElem);
    },
    allAccountSelectorCallback:function(){}
};

</script>
<style>
    [kr-accounts-wrapper] {
        line-height: 26px;
    }
    [kr-accounts-wrapper] .kr-accounts__selector{
        width: 200px;
        font-size: 10px;
        margin: 0;
        font-weight: 400;
    }
    [kr-accounts-wrapper] .kr-accounts__selector span{
        font-size: 12px;
        margin: 0;
        font-weight: 400;
        width: 100%;
        display: block;
        text-align: right;
        line-height: 18px;
        color: #000;
    }
    [kr-accounts-wrapper] .kr-accounts__selector .select2-selection__rendered{
        padding: 0 10px !important;
        text-align: left !important;
        line-height: 1.7 !important;
    }
    [kr-accounts-wrapper] .kr-accounts__selector .select2-selection__arrow{
        height: 84% !important;
    }

    [kr-accounts-wrapper] .kr-accounts__selector .select2-selection{
        height:auto !important;
    }

    [kr-accounts-wrapper] .kr-accounts__amount{
        font-size: 25px;
        color: #08976d;
        font-weight: 500;
        letter-spacing: 1px;
        text-align: right;
    }
    [kr-accounts-wrapper] .kr-accounts__btn-balance{
        padding: 2px 9px;
    }
    [kr-accounts-wrapper] .kr-accounts__selector select{
        width: 100%;
    }
    [kr-accounts-wrapper].kr-accounts--loading{
        opacity: .5;
        pointer-events: none;
    }
    [kr-accounts-wrapper][data-hide-balance] .kr-accounts__amount{
        display: none;
    }
</style>
<script>

if(!Element.prototype.trigger){
    Element.prototype.trigger = function (type) {
        var elem = this;
        var event = document.createEvent("MouseEvents");
        event.initMouseEvent(type, true, true, elem.ownerDocument.defaultView,
            0, 0, 0, 0, 0, false, false, false, false, 0, null);
        elem.dispatchEvent(event);
    }
}

"undefined" == typeof window.KR_ACCOUNTS && (window.KR_ACCOUNTS = {});

/* Utils */
KR_ACCOUNTS.Utils={
    setCookie:function(cname, cvalue, exdays){
        var d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        var expires = "expires="+d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    },
    deleteCookie:function(cname){
        var expires = "expires=Thu, 01 Jan 1970 00:00:00 UTC";
        document.cookie = cname + "=" + ";" + expires + ";path=/";
    },
    getCookie:function(cname){
        var name = cname + "=";
        var ca = document.cookie.split(';');
        for(var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') {
            c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
            }
        }
        return "";
    },
};

/* Settings for account selector */
KR_ACCOUNTS.Settings = {
    balance:function(){

        var selected_account = this.getSelectedAccount();

        return selected_account.balance;
    },

    getSelectedAccount(){

        var accounts = KR_ACCOUNTS.Settings.accounts;
        if(accounts.length==0)return 0;

        /* gets selected account */
        var selected_account_id=KR_ACCOUNTS.Settings.selected_account;
        var selected_account=null;

        /* check if account find, else select 1st account */
        if(selected_account_id)selected_account = accounts.find(function(x){return x._id==selected_account_id});
        else selected_account = accounts[0];

        return selected_account;

    },

    selected_account: KR_ACCOUNTS.Utils.getCookie('kr_account_selected')||null,
    accounts:[],
    account_fetching:false,
    account_fetched:false
};

KR_ACCOUNTS.Modules={
    /* USE headers:{'X-NOFETCH':''} when you want not to fetch accounts on POST requests */
    account_selector:function(){

        return {
            events:{
                onchange:function($this){
                    /* update account id */
                    KR_ACCOUNTS.Settings.selected_account=$this.value;
                    KR_ACCOUNTS.Modules.account_selector.update_selectedaccount();

                    /* resent ajax and refresh the selector - Only for [kr-accounts-wrapper] */
                    if(!$this.closest('[kr-accounts-dropdown-wrapper]')){
                        KR_ACCOUNTS.Modules.account_selector.init(true);
                    }

                }
            },
            validate:function(amount){
                amount=parseFloat(amount)||0;
                var balance= KR_ACCOUNTS.Settings.balance();
                return amount<=balance;
            },
            update_selectedaccount:function(){
                var selected_account_id=KR_ACCOUNTS.Settings.selected_account;

                if(selected_account_id){
                    /* store this id in cookie, for 30 days */
                    KR_ACCOUNTS.Utils.setCookie('kr_account_selected',selected_account_id,30);
                }
            },
            generate_html:function(){
                var html='';
                var accounts = KR_ACCOUNTS.Settings.accounts;
                if(accounts.length){

                    var selected_account_id=KR_ACCOUNTS.Settings.selected_account;
                    var selected_account=null;



                    /* check if account find, else select 1st account */
                    if(selected_account_id){
                        selected_account = accounts.find(function(x){return x._id==selected_account_id});

                        /* check if account if from cookie doesn't exists */
                        if(typeof selected_account == "undefined"){
                            /* it seems account from cookie is wrong, delete the cookie */
                            KR_ACCOUNTS.Utils.deleteCookie('kr_account_selected');
                            selected_account = accounts[0];

                        }
                    }
                    else selected_account = accounts[0];

                    /* update account id */
                    KR_ACCOUNTS.Settings.selected_account=selected_account._id;
                    KR_ACCOUNTS.Modules.account_selector.update_selectedaccount();

                    /* Generate HTML and append these accounts */
                    var selectHtml = '';

                    var dropdownHtml = '<select required class="'+KR_ACCOUNTS_CONFIG.accountSelectorClassList+'" onchange="KR_ACCOUNTS.Modules.account_selector.events.onchange(this)">';
                    accounts.forEach(function(account,i){
                        var is_selected = selected_account._id==account._id?'selected':'';
                        dropdownHtml += '<option value="'+account._id+'" '+is_selected+'>'+account.title+'</option>';
                    });
                    dropdownHtml+='</select>';

                    if(accounts.length==1){
                        /* only 1 account returned, we might not need select dropdown here */
                        selectHtml='<span>CASH IN HAND</span>';
                    }
                    else{
                        selectHtml = dropdownHtml;
                    }
                    html=''+
                    '<div class="kr-accounts__selector">'+selectHtml+'</div>'+
                    '<div class="kr-accounts__amount" data-hidden-amount="AED '+selected_account.balance+'">'+
                    '   <button class="btn btn-outline-info btn-sm kr-accounts__btn-balance" onclick="this.outerHTML=this.parentNode.getAttribute(\'data-hidden-amount\')"> <i class="flaticon-eye"></i> Show Balance </button>'+
                    '</div>';

                    /* append html */
                    var selector = document.querySelectorAll('[kr-accounts-wrapper]');
                    selector.forEach(function(elem,index){
                        elem.innerHTML = html;

                        /* call for plugin update (select2) */
                        if(typeof KR_ACCOUNTS_CONFIG !== "undefined" && typeof KR_ACCOUNTS_CONFIG.eachAccountSelectorCallback=="function" && elem.querySelector('select')) KR_ACCOUNTS_CONFIG.eachAccountSelectorCallback(elem.querySelector('select'));
                    });

                    /* append dropdown html */
                    selector = document.querySelectorAll('[kr-accounts-dropdown-wrapper]');
                    selector.forEach(function(elem,index){
                        elem.innerHTML = dropdownHtml;

                        if(elem.hasAttribute('kr-accounts-unselect') && elem.getAttribute('kr-accounts-unselect') == 1){
                            elem.querySelector('select').selectedIndex = -1;
                        }


                        if(elem.hasAttribute('kr-accounts-name')){
                            elem.querySelector('select')
                            .setAttribute('name', elem.getAttribute('kr-accounts-name'));

                            elem.querySelector('select')
                            .removeAttribute('onchange');
                        }

                        if(elem.hasAttribute('kr-accounts-selected') && !!elem.getAttribute('kr-accounts-selected')){
                            elem.querySelectorAll('select option').forEach(function(el){el.selected = false});

                            elem.querySelector('select option[value="'+elem.getAttribute('kr-accounts-selected')+'"]')
                            .selected = true;

                            /* update account id */
                            KR_ACCOUNTS.Settings.selected_account=elem.getAttribute('kr-accounts-selected');
                            KR_ACCOUNTS.Modules.account_selector.update_selectedaccount();

                            // remove attribute so next time it wont run
                            elem.removeAttribute('kr-accounts-selected')
                        }

                        /* call for plugin update (select2) */
                        if(typeof KR_ACCOUNTS_CONFIG !== "undefined" && typeof KR_ACCOUNTS_CONFIG.eachAccountSelectorCallback=="function" && elem.querySelector('select')) KR_ACCOUNTS_CONFIG.eachAccountSelectorCallback(elem.querySelector('select'));
                    });

                    if(typeof KR_ACCOUNTS_CONFIG !== "undefined" && typeof KR_ACCOUNTS_CONFIG.allAccountSelectorCallback=="function")KR_ACCOUNTS_CONFIG.allAccountSelectorCallback();
                }
                else{
                    /* seems no account found, delete account cookie */
                    KR_ACCOUNTS.Utils.deleteCookie('kr_account_selected');
                }
            },
            init:function(force_refresh=false){



                /* if already request in process, just let it complete */
                if(KR_ACCOUNTS.Settings.account_fetching)return;


                /* check if accounts already retrieved, we might need to update the selector only rather than again */
                if(KR_ACCOUNTS.Settings.account_fetched && !force_refresh){

                    KR_ACCOUNTS.Modules.account_selector.generate_html();
                    return;
                }
                /* add 'loading' class to all selectors */
                document.querySelectorAll('[kr-accounts-wrapper]').forEach(function(elem, i){elem.classList.add('kr-accounts--loading')});

                /* we need to send ajax and get all accounts */
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {

                    if (this.readyState == 4 && this.status == 200) {

                        KR_ACCOUNTS.Settings.account_fetching=false;

                        /* remove 'loading' class from all account selectors */
                        document.querySelectorAll('[kr-accounts-wrapper]').forEach(function(elem, i){elem.classList.remove('kr-accounts--loading')});



                        var jsonResponse = JSON.parse(this.responseText);
                        KR_ACCOUNTS.Settings.account_fetched=true;

                        if(jsonResponse.status==0){
                            /* some error occured */
                            console.error('Account error', jsonResponse.msg);
                        }
                        else{

                            var selector = document.querySelectorAll('[kr-accounts-wrapper]');

                            /* generate account selector and appends */
                            KR_ACCOUNTS.Settings.accounts = jsonResponse.accounts;

                            KR_ACCOUNTS.Modules.account_selector.generate_html();

                        }

                        /* apply restriction on all input */
                        KR_ACCOUNTS.Modules.handle_input.apply_on_all();
                    }
                };
                xhttp.open("GET", "{{route('module.accounts.fetchaccounts')}}", true);
                KR_ACCOUNTS.Settings.account_fetching=true;
                xhttp.setRequestHeader("Content-Type", "application/json");
                xhttp.send();
            }
        };
    }(),

    handle_input:function(){

        return {
            listen:function(){
                /* listen to all input on DOM, so we can catch dynamically added input too with this */
                document.addEventListener("DOMContentLoaded", function(event) {
                    // Your code to run since DOM is loaded and ready
                    document.body.addEventListener('input',function(e){
                        var input = e.target;
                        KR_ACCOUNTS.Modules.handle_input.init(input);
                    });
                });

            },
            init:function(input){
                /* check if this input is eligible for accounts (has "kr-accounts-input" attribute) */
                if(input && input.getAttribute('kr-accounts-input') != null ){
                    /* remove error elements */
                    document.querySelectorAll('p.kr-accounts__input--error').length && (document.querySelectorAll('p.kr-accounts__input--error').forEach(function(elem){elem.remove()}));

                    //  ------------------------
                    //  Negative Balance Access
                    //  ------------------------
                    var validateBalance = function(){
                        //an input with name="amount" found, we need to check cih
                        var have_amount = KR_ACCOUNTS.Settings.balance();
                        var _val = parseFloat(input.value)||0;



                        if(_val>have_amount){
                            /* set the max amount */
                            input.value=have_amount;

                            /* show error */
                            var _errElem = document.createElement("p");   // Create a <button> element
                            _errElem.innerHTML = 'Amount exceeded!  Cash in hand is AED. '+have_amount;
                            _errElem.classList.add("text-danger");
                            _errElem.classList.add("kr-accounts__input--error");
                            input.parentNode.insertBefore(_errElem, input.nextSibling);
                            input.trigger('change');

                        }
                    }


                    @unless ($helper_service->helper->isSuperUser() || $negativeAccess->all === true)


                        // Check if selected account has access
                        // only then we need to apply amount restrictions
                        var selectedAccount = KR_ACCOUNTS.Settings.getSelectedAccount();
                        let granted_account_ids = @json($negativeAccess->ids);

                        if(!granted_account_ids.includes(selectedAccount._id)){
                            validateBalance();
                        }


                    @endunless

                }
            },
            apply_on_all:function(){
                document.querySelectorAll('input[kr-accounts-input]').forEach(function(elem){
                    KR_ACCOUNTS.Modules.handle_input.init(elem);
                });
            }
        };
    }()



};

(function(){

    /* store local time in cookie so we can get it from server any time */
    var localUtcOffset = -new Date().getTimezoneOffset();
    var timecookie = KR_ACCOUNTS.Utils.getCookie('kra_utcoffset');
    if(timecookie=="")KR_ACCOUNTS.Utils.setCookie('kra_utcoffset', localUtcOffset, 1);

    /* Hook on all ajax calls */
    var origOpen = XMLHttpRequest.prototype.open;
    XMLHttpRequest.prototype.open = function() {
        var method=arguments[0];
        this.addEventListener('load', function() {
            /* if request is POST, we should update the accounts amount, maybe some amount is added/deducted */
            var forceUpdate=false;
            if(method.toLowerCase()=='post'){
                forceUpdate=true;

                /* check if request don't want to fetch account even if it is POST request */
                if(typeof this.getResponseHeader === "function"&&this.getResponseHeader("X-NOFETCH") === "Y")forceUpdate=false;
            }

            /* we need to check if html elements are present in response, then we will find for account selector */
            if(/<\/?[a-z][\s\S]*>/i.test(this.responseText)){
                var response = this.responseText;

                /* check if account selector element found */
                if(response.search('kr-accounts-wrapper')>-1){
                    /* wait for some time so any modal is loaded */
                    setTimeout(function(){
                        KR_ACCOUNTS.Modules.account_selector.init(forceUpdate);
                    }, 500);
                    return;
                }
            }

            if(forceUpdate){
                setTimeout(function(){
                    KR_ACCOUNTS.Modules.account_selector.init(!0);
                }, 500);
            }


        });
        origOpen.apply(this, arguments);
    };

    KR_ACCOUNTS.Modules.account_selector.init();

    /* listen for input for amount restricction */
    KR_ACCOUNTS.Modules.handle_input.listen();

})();

</script>
