@extends('Tenant.layouts.app')

@section('page_title')
    Create Driver
@endsection
@section('head')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/css/intlTelInput.css">
    <link rel="stylesheet" href="{{ asset('theme/css/pages/wizards/wizard-v3.css') }}">
    <style>


        #kt-portlet__create-driver .kt-wizard-v3 [data-custom-ktwizard-type=action-submit],.kt-wizard-v3[data-ktwizard-state=first] [data-custom-ktwizard-type=action-prev] {
            display: none
        }

        #kt-portlet__create-driver .kt-wizard-v3[data-ktwizard-state=first] [data-custom-ktwizard-type=action-next] {
            display: inline-block
        }

        #kt-portlet__create-driver .kt-wizard-v3[data-ktwizard-state=first] [data-custom-ktwizard-type=action-submit] {
            display: none
        }

        #kt-portlet__create-driver .kt-wizard-v3[data-ktwizard-state=between] [data-custom-ktwizard-type=action-next],.kt-wizard-v3[data-ktwizard-state=between] [data-custom-ktwizard-type=action-prev] {
            display: inline-block
        }

        #kt-portlet__create-driver .kt-wizard-v3[data-ktwizard-state=between] [data-custom-ktwizard-type=action-submit] {
            display: none
        }

        #kt-portlet__create-driver .kt-wizard-v3[data-ktwizard-state=last] [data-custom-ktwizard-type=action-prev] {
            display: inline-block
        }

        #kt-portlet__create-driver .kt-wizard-v3[data-ktwizard-state=last] [data-custom-ktwizard-type=action-next] {
            display: none
        }

        #kt-portlet__create-driver .kt-wizard-v3[data-ktwizard-state=last] [data-custom-ktwizard-type=action-submit] {
            display: inline-block
        }


        #kt-portlet__create-driver .kt-wizard-v3 .kt-form .kt-form__actions [data-custom-ktwizard-type=action-prev] {
            margin-right: auto
        }

        @media (max-width: 576px) {
            #kt-portlet__create-driver .kt-wizard-v3 .kt-form .kt-form__actions [data-custom-ktwizard-type=action-prev] {
                margin-right:.5rem
            }
        }

        #kt-portlet__create-driver .kt-wizard-v3 .kt-form .kt-form__actions [data-custom-ktwizard-type=action-next] {
            margin: auto 0 auto auto
        }

        @media (max-width: 576px) {
            #kt-portlet__create-driver .kt-wizard-v3 .kt-form .kt-form__actions [data-custom-ktwizard-type=action-next] {
                margin:0 0 1rem
            }
        }

        #kt-portlet__create-driver .iti {
            width: 100%;
        }

        /* Adjust the width of the input field to be 100% of the ITI container */
        #kt-portlet__create-driver .iti__input {
            width: 100%;
        }
        #kt-portlet__create-driver .driver__phone-error{
            color:#fd397a!important;
            font-weight: bold;
        }
        #kt-portlet__create-driver .driver__phone{
            color:#1dc9b7;
        }

        #kt-portlet__create-driver .addon-setting-container{
            min-height: 200px;
        }
        #kt-portlet__create-driver table.datatable thead th{
            padding: 5px 8px;
            font-weight: bold;
        }
        #kt-portlet__create-driver table.datatable tbody td{
            padding: 5px 5px;
        }
        #kt-portlet__create-driver .job-content input,
        #kt-portlet__create-driver .job-content table.datatable tbody textarea{
            padding: 4px 7px;
            height: auto;
        }
        #kt-portlet__create-driver table.datatable tbody .btndelete{
            height: 1.5rem;
            width: 1.5rem;
        }

        #kt-portlet__create-driver table.datatable thead{
            background: #f9f9f9;
        }
        #kt-portlet__create-driver table.datatable thead th:nth-of-type(2) {
            width:40%;
        }
        #kt-portlet__create-driver table.datatable thead th:nth-of-type(3) {
            width:30%;
        }
        #kt-portlet__create-driver table.datatable thead th:nth-of-type(4) {
            width:20%;
        }
        #kt-portlet__create-driver table.datatable thead th:nth-of-type(5) {
            width:10%;
        }
        #kt-portlet__create-driver table.datatable tbody tr td {
            vertical-align:middle;
            text-align:center;
        }
    </style>
@endsection
@section('content')
    <!--begin::Portlet-->
    <div class="kt-portlet mt-5" id="kt-portlet__create-driver" kr-ajax-content>
        <div class="kt-portlet__body kt-portlet__body--fit">

            <div class="kt-wizard-v3" id="wizard_driver" data-ktwizard-state="step-first">

                <!-- --------------------------- -->
                <!--      begin: NAVIGATION      -->
                <!-- --------------------------- -->

                <div class="kt-wizard-v3__nav">
                    <div class="kt-wizard-v3__nav-line"></div>
                    <div class="kt-wizard-v3__nav-items">

                        <!--doc: Replace A tag with SPAN tag to disable the step link click -->
                        <a class="kt-wizard-v3__nav-item" href="#" data-ktwizard-type="step" data-ktwizard-state="current">
                            <span>1</span>
                            <i class="fa fa-check"></i>
                            <div class="kt-wizard-v3__nav-label">Basic Details</div>
                        </a>
                        <a class="kt-wizard-v3__nav-item" href="#" data-ktwizard-type="step">
                            <span>2</span>
                            <i class="fa fa-check"></i>
                            <div class="kt-wizard-v3__nav-label">Visa Details</div>
                        </a>
                        <a class="kt-wizard-v3__nav-item" href="#" data-ktwizard-type="step">
                            <span>3</span>
                            <i class="fa fa-check"></i>
                            <div class="kt-wizard-v3__nav-label">License Details</div>
                        </a>
                        <a class="kt-wizard-v3__nav-item" href="#" data-ktwizard-type="step">
                            <span>4</span>
                            <i class="fa fa-check"></i>
                            <div class="kt-wizard-v3__nav-label">RTA Details</div>
                        </a>
                        <a class="kt-wizard-v3__nav-item" href="#" data-ktwizard-type="step">
                            <span>5</span>
                            <i class="fa fa-check"></i>
                            <div class="kt-wizard-v3__nav-label">Finalize</div>
                        </a>
                    </div>
                </div>

                <!-- --------------------------- -->
                <!--      end: NAVIGATION        -->
                <!-- --------------------------- -->


                <!-- --------------------------- -->
                <!--        begin: FORM          -->
                <!-- --------------------------- -->
                <form class="kt-form" enctype="multipart/form-data" action="{{ route('tenant.admin.drivers.add') }}" method="POST">
                    @csrf

                    {{-- ----------------- --}}
                    {{--   Basic Details   --}}
                    {{-- ----------------- --}}
                    <div class="kt-wizard-v3__content" data-ktwizard-type="step-content" data-ktwizard-state="current">
                        <div class="kt-heading kt-heading--md">Basic Details</div>

                        <div class="kt-separator kt-separator--height-xs"></div>

                        <div class="kt-form__section kt-form__section--first">
                            {{-- Type --}}
                            <div class="form-group">
                                <label>Select Type <span class="text-danger">*<span></label>
                                <div class="kt-radio-inline">
                                    <label class="kt-radio">
                                        <input type="radio" @if($type === 'rider') checked @endif value="rider" name="type" required> Rider
                                        <span></span>
                                    </label>
                                    <label class="kt-radio">
                                        <input type="radio" value="driver" @if($type === 'driver') checked @endif name="type" required> Driver
                                        <span></span>
                                    </label>
                                </div>
                                <span class="form-text text-muted">select Driver or Rider</span>
                            </div>

                            {{-- Name --}}
                            <div class="form-group">
                                <label>Name <span class="text-danger">*<span></label>
                                <input type="text" autocomplete="off" name="name" required class="form-control @error('name') is-invalid @enderror" placeholder="Enter Name" value="{{ old('name') }}">
                                @if ($errors->has('name'))
                                    <span class="invalid-response text-danger" role="alert">
                                        <strong>
                                            {{ $errors->first('name') }}
                                        </strong>
                                    </span>
                                @endif
                            </div>

                            {{-- Email --}}
                            <div class="form-group">
                                <label>Email <span class="text-danger">*<span></label>
                                <input type="email" autocomplete="off" name="email" required class="form-control @error('email') is-invalid @enderror" placeholder="Enter Email" value="{{ old('email') }}">
                                @if ($errors->has('email'))
                                    <span class="invalid-response text-danger" role="alert">
                                        <strong>
                                            {{ $errors->first('email') }}
                                        </strong>
                                    </span>
                                @endif

                            </div>

                            {{-- Date of Birth --}}
                            <div class="form-group">
                                <label>Date of Birth </label>
                                <input type="text" readonly name="date_of_birth" data-state="date" class="kr-datepicker form-control @error('date_of_birth') is-invalid @enderror" data-default="{{ old('date_of_birth') }}">
                                @if ($errors->has('date_of_birth'))
                                    <span class="invalid-response text-danger" role="alert">
                                        <strong>
                                            {{ $errors->first('date_of_birth') }}
                                        </strong>
                                    </span>
                                @endif

                            </div>

                            {{-- Phone Number --}}
                            <div class="form-group">
                                <label>Phone Number</label>
                                <input type="tel" autocomplete="off" name="phone_number" class="form-control @error('full_phone') is-invalid @enderror" value="{{ old('full_phone') }}">
                                <span id="phone-error-msg" class="driver__phone m-0 mt-2"></span>

                            </div>

                            {{-- Profile Picture --}}
                            <div class="form-group">
                                <label>Uplaod Profile Picture </label>
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div class="kt-uppy kr-uppy" uppy-size="20" uppy-max="1" uppy-min="1"
                                            uppy-label="Profile Picture" uppy-input="profile_picture"></div>
                                        <span class="form-text text-muted">Max file size is 20MB and max number of files is 1.</span>
                                        @if ($errors->has('profile_picture'))
                                            <span class="invalid-response text-danger" role="alert">
                                                <strong>
                                                    {{ $errors->first('profile_picture') }}
                                                </strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Passport Collection --}}
                            <div class="form-group">
                                <label>Passport Collected? </label>
                                <div class="col-3">
                                    <span class="kt-switch">
                                        <label>
                                            <input type="checkbox" name="is_pasport_collected">
                                            <span></span>
                                        </label>
                                    </span>
                                </div>
                            </div>

                            {{-- Passport Number --}}
                            <div class="form-group">
                                <label>Passport Number <span class="text-danger">*</span></label>
                                <input type="text" required autocomplete="off" name="passport_number" class="form-control @error('passport_number') is-invalid @enderror" placeholder="Enter Passport" value="{{ old('passport_number') }}">
                                @if ($errors->has('passport_number'))
                                    <span class="invalid-response text-danger" role="alert">
                                        <strong>
                                            {{ $errors->first('passport_number') }}
                                        </strong>
                                    </span>
                                @endif
                            </div>

                            {{-- Passport Images --}}
                            <div class="form-group">
                                <label>Uplaod Passport Picture </label>
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div class="kt-uppy kr-uppy" uppy-size="20" uppy-max="1" uppy-min="1" uppy-label="Passport Picture" uppy-input="passport_pictures_front"></div>
                                        <span class="form-text text-muted">Max file size is 20MB and max number of files is 1.</span>
                                        @if ($errors->has('passport_pictures_front'))
                                            <span class="invalid-response text-danger" role="alert">
                                                <strong>
                                                    {{ $errors->first('passport_pictures_front') }}
                                                </strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Passport Expiry --}}
                            <div class="form-group">
                                <label>Passport Expiry </label>
                                <input type="text" readonly name="passport_expiry" data-state="date" class="kr-datepicker form-control @error('passport_expiry') is-invalid @enderror" data-default="{{ old('passport_expiry') }}">
                                @if ($errors->has('passport_expiry'))
                                    <span class="invalid-response text-danger" role="alert">
                                        <strong>
                                            {{ $errors->first('passport_expiry') }}
                                        </strong>
                                    </span>
                                @endif

                            </div>



                            {{-- Location --}}
                            <div class="form-group">
                                <label>Location <span class="text-danger">*</span></label>
                                @include('Tenant.includes.locations')
                            </div>


                            {{-- Nationality --}}
                            <div class="form-group">
                                <label>Nationality </label>
                                <select class="form-control kr-select2 @if ($errors->has('nationality')) invalid-field @endif" name="nationality">
                                    <option value=""></option>
                                    <option @if (old('nationality') === 'andorran') selected @endif value="andorran">Andorran</option>
                                    <option @if (old('nationality') === 'angolan') selected @endif value="angolan">Angolan</option>
                                    <option @if (old('nationality') === 'antiguans') selected @endif value="antiguans">Antiguans</option>
                                    <option @if (old('nationality') === 'argentinean') selected @endif value="argentinean">Argentinean</option>
                                    <option @if (old('nationality') === 'afghan') selected @endif value="afghan">Afghan</option>
                                    <option @if (old('nationality') === 'albanian') selected @endif value="albanian">Albanian</option>
                                    <option @if (old('nationality') === 'algerian') selected @endif value="algerian">Algerian</option>
                                    <option @if (old('nationality') === 'american') selected @endif value="american">American</option>
                                    <option @if (old('nationality') === 'armenian') selected @endif value="armenian">Armenian</option>
                                    <option @if (old('nationality') === 'australian') selected @endif value="australian">Australian</option>
                                    <option @if (old('nationality') === 'austrian') selected @endif value="austrian">Austrian</option>
                                    <option @if (old('nationality') === 'azerbaijani') selected @endif value="azerbaijani">Azerbaijani</option>
                                    <option @if (old('nationality') === 'bahamian') selected @endif value="bahamian">Bahamian</option>
                                    <option @if (old('nationality') === 'bahraini') selected @endif value="bahraini">Bahraini</option>
                                    <option @if (old('nationality') === 'bangladeshi') selected @endif value="bangladeshi">Bangladeshi</option>
                                    <option @if (old('nationality') === 'barbadian') selected @endif value="barbadian">Barbadian</option>
                                    <option @if (old('nationality') === 'barbudans') selected @endif value="barbudans">Barbudans</option>
                                    <option @if (old('nationality') === 'batswana') selected @endif value="batswana">Batswana</option>
                                    <option @if (old('nationality') === 'belarusian') selected @endif value="belarusian">Belarusian</option>
                                    <option @if (old('nationality') === 'belgian') selected @endif value="belgian">Belgian</option>
                                    <option @if (old('nationality') === 'belizean') selected @endif value="belizean">Belizean</option>
                                    <option @if (old('nationality') === 'beninese') selected @endif value="beninese">Beninese</option>
                                    <option @if (old('nationality') === 'bhutanese') selected @endif value="bhutanese">Bhutanese</option>
                                    <option @if (old('nationality') === 'bolivian') selected @endif value="bolivian">Bolivian</option>
                                    <option @if (old('nationality') === 'bosnian') selected @endif value="bosnian">Bosnian</option>
                                    <option @if (old('nationality') === 'brazilian') selected @endif value="brazilian">Brazilian</option>
                                    <option @if (old('nationality') === 'british') selected @endif value="british">British</option>
                                    <option @if (old('nationality') === 'bruneian') selected @endif value="bruneian">Bruneian</option>
                                    <option @if (old('nationality') === 'bulgarian') selected @endif value="bulgarian">Bulgarian</option>
                                    <option @if (old('nationality') === 'burkinabe') selected @endif value="burkinabe">Burkinabe</option>
                                    <option @if (old('nationality') === 'burmese') selected @endif value="burmese">Burmese</option>
                                    <option @if (old('nationality') === 'burundian') selected @endif value="burundian">Burundian</option>
                                    <option @if (old('nationality') === 'cambodian') selected @endif value="cambodian">Cambodian</option>
                                    <option @if (old('nationality') === 'cameroonian') selected @endif value="cameroonian">Cameroonian</option>
                                    <option @if (old('nationality') === 'canadian') selected @endif value="canadian">Canadian</option>
                                    <option @if (old('nationality') === 'cape') selected @endif value="cape verdean">Cape Verdean</option>
                                    <option @if (old('nationality') === 'central') selected @endif value="central african">Central African</option>
                                    <option @if (old('nationality') === 'chadian') selected @endif value="chadian">Chadian</option>
                                    <option @if (old('nationality') === 'chilean') selected @endif value="chilean">Chilean</option>
                                    <option @if (old('nationality') === 'chinese') selected @endif value="chinese">Chinese</option>
                                    <option @if (old('nationality') === 'colombian') selected @endif value="colombian">Colombian</option>
                                    <option @if (old('nationality') === 'comoran') selected @endif value="comoran">Comoran</option>
                                    <option @if (old('nationality') === 'congolese') selected @endif value="congolese">Congolese</option>
                                    <option @if (old('nationality') === 'costa') selected @endif value="costa rican">Costa Rican</option>
                                    <option @if (old('nationality') === 'croatian') selected @endif value="croatian">Croatian</option>
                                    <option @if (old('nationality') === 'cuban') selected @endif value="cuban">Cuban</option>
                                    <option @if (old('nationality') === 'cypriot') selected @endif value="cypriot">Cypriot</option>
                                    <option @if (old('nationality') === 'czech') selected @endif value="czech">Czech</option>
                                    <option @if (old('nationality') === 'danish') selected @endif value="danish">Danish</option>
                                    <option @if (old('nationality') === 'djibouti') selected @endif value="djibouti">Djibouti</option>
                                    <option @if (old('nationality') === 'dominican') selected @endif value="dominican">Dominican</option>
                                    <option @if (old('nationality') === 'dutch') selected @endif value="dutch">Dutch</option>
                                    <option @if (old('nationality') === 'east') selected @endif value="east timorese">East Timorese</option>
                                    <option @if (old('nationality') === 'ecuadorean') selected @endif value="ecuadorean">Ecuadorean</option>
                                    <option @if (old('nationality') === 'egyptian') selected @endif value="egyptian">Egyptian</option>
                                    <option @if (old('nationality') === 'emirian') selected @endif value="emirian">Emirian</option>
                                    <option @if (old('nationality') === 'equatorial') selected @endif value="equatorial guinean">EquatorialGuinean</option>
                                    <option @if (old('nationality') === 'eritrean') selected @endif value="eritrean">Eritrean</option>
                                    <option @if (old('nationality') === 'estonian') selected @endif value="estonian">Estonian</option>
                                    <option @if (old('nationality') === 'ethiopian') selected @endif value="ethiopian">Ethiopian</option>
                                    <option @if (old('nationality') === 'fijian') selected @endif value="fijian">Fijian</option>
                                    <option @if (old('nationality') === 'filipino') selected @endif value="filipino">Filipino</option>
                                    <option @if (old('nationality') === 'finnish') selected @endif value="finnish">Finnish</option>
                                    <option @if (old('nationality') === 'french') selected @endif value="french">French</option>
                                    <option @if (old('nationality') === 'gabonese') selected @endif value="gabonese">Gabonese</option>
                                    <option @if (old('nationality') === 'gambian') selected @endif value="gambian">Gambian</option>
                                    <option @if (old('nationality') === 'georgian') selected @endif value="georgian">Georgian</option>
                                    <option @if (old('nationality') === 'german') selected @endif value="german">German</option>
                                    <option @if (old('nationality') === 'ghanaian') selected @endif value="ghanaian">Ghanaian</option>
                                    <option @if (old('nationality') === 'greek') selected @endif value="greek">Greek</option>
                                    <option @if (old('nationality') === 'grenadian') selected @endif value="grenadian">Grenadian</option>
                                    <option @if (old('nationality') === 'guatemalan') selected @endif value="guatemalan">Guatemalan</option>
                                    <option @if (old('nationality') === 'guinea') selected @endif value="guinea-bissauan">Guinea-Bissauan</option>
                                    <option @if (old('nationality') === 'guinean') selected @endif value="guinean">Guinean</option>
                                    <option @if (old('nationality') === 'guyanese') selected @endif value="guyanese">Guyanese</option>
                                    <option @if (old('nationality') === 'haitian') selected @endif value="haitian">Haitian</option>
                                    <option @if (old('nationality') === 'herzegovinian') selected @endif value="herzegovinian">Herzegovinian</option>
                                    <option @if (old('nationality') === 'honduran') selected @endif value="honduran">Honduran</option>
                                    <option @if (old('nationality') === 'hungarian') selected @endif value="hungarian">Hungarian</option>
                                    <option @if (old('nationality') === 'icelander') selected @endif value="icelander">Icelander</option>
                                    <option @if (old('nationality') === 'indian') selected @endif value="indian">Indian</option>
                                    <option @if (old('nationality') === 'indonesian') selected @endif value="indonesian">Indonesian</option>
                                    <option @if (old('nationality') === 'iranian') selected @endif value="iranian">Iranian</option>
                                    <option @if (old('nationality') === 'iraqi') selected @endif value="iraqi">Iraqi</option>
                                    <option @if (old('nationality') === 'irish') selected @endif value="irish">Irish</option>
                                    <option @if (old('nationality') === 'israeli') selected @endif value="israeli">Israeli</option>
                                    <option @if (old('nationality') === 'italian') selected @endif value="italian">Italian</option>
                                    <option @if (old('nationality') === 'ivorian') selected @endif value="ivorian">Ivorian</option>
                                    <option @if (old('nationality') === 'jamaican') selected @endif value="jamaican">Jamaican</option>
                                    <option @if (old('nationality') === 'japanese') selected @endif value="japanese">Japanese</option>
                                    <option @if (old('nationality') === 'jordanian') selected @endif value="jordanian">Jordanian</option>
                                    <option @if (old('nationality') === 'kazakhstani') selected @endif value="kazakhstani">Kazakhstani</option>
                                    <option @if (old('nationality') === 'kenyan') selected @endif value="kenyan">Kenyan</option>
                                    <option @if (old('nationality') === 'kittian_nevisian') selected @endif value="kittian_nevisian">Kittian and Nevisian</option>
                                    <option @if (old('nationality') === 'kuwaiti') selected @endif value="kuwaiti">Kuwaiti</option>
                                    <option @if (old('nationality') === 'kyrgyz') selected @endif value="kyrgyz">Kyrgyz</option>
                                    <option @if (old('nationality') === 'laotian') selected @endif value="laotian">Laotian</option>
                                    <option @if (old('nationality') === 'latvian') selected @endif value="latvian">Latvian</option>
                                    <option @if (old('nationality') === 'lebanese') selected @endif value="lebanese">Lebanese</option>
                                    <option @if (old('nationality') === 'liberian') selected @endif value="liberian">Liberian</option>
                                    <option @if (old('nationality') === 'libyan') selected @endif value="libyan">Libyan</option>
                                    <option @if (old('nationality') === 'liechtensteiner') selected @endif value="liechtensteiner">Liechtensteiner</option>
                                    <option @if (old('nationality') === 'lithuanian') selected @endif value="lithuanian">Lithuanian</option>
                                    <option @if (old('nationality') === 'luxembourger') selected @endif value="luxembourger">Luxembourger</option>
                                    <option @if (old('nationality') === 'macedonian') selected @endif value="macedonian">Macedonian</option>
                                    <option @if (old('nationality') === 'malagasy') selected @endif value="malagasy">Malagasy</option>
                                    <option @if (old('nationality') === 'malawian') selected @endif value="malawian">Malawian</option>
                                    <option @if (old('nationality') === 'malaysian') selected @endif value="malaysian">Malaysian</option>
                                    <option @if (old('nationality') === 'maldivan') selected @endif value="maldivan">Maldivan</option>
                                    <option @if (old('nationality') === 'malian') selected @endif value="malian">Malian</option>
                                    <option @if (old('nationality') === 'maltese') selected @endif value="maltese">Maltese</option>
                                    <option @if (old('nationality') === 'marshallese') selected @endif value="marshallese">Marshallese</option>
                                    <option @if (old('nationality') === 'mauritanian') selected @endif value="mauritanian">Mauritanian</option>
                                    <option @if (old('nationality') === 'mauritian') selected @endif value="mauritian">Mauritian</option>
                                    <option @if (old('nationality') === 'mexican') selected @endif value="mexican">Mexican</option>
                                    <option @if (old('nationality') === 'micronesian') selected @endif value="micronesian">Micronesian</option>
                                    <option @if (old('nationality') === 'moldovan') selected @endif value="moldovan">Moldovan</option>
                                    <option @if (old('nationality') === 'monacan') selected @endif value="monacan">Monacan</option>
                                    <option @if (old('nationality') === 'mongolian') selected @endif value="mongolian">Mongolian</option>
                                    <option @if (old('nationality') === 'moroccan') selected @endif value="moroccan">Moroccan</option>
                                    <option @if (old('nationality') === 'mosotho') selected @endif value="mosotho">Mosotho</option>
                                    <option @if (old('nationality') === 'motswana') selected @endif value="motswana">Motswana</option>
                                    <option @if (old('nationality') === 'mozambican') selected @endif value="mozambican">Mozambican</option>
                                    <option @if (old('nationality') === 'namibian') selected @endif value="namibian">Namibian</option>
                                    <option @if (old('nationality') === 'nauruan') selected @endif value="nauruan">Nauruan</option>
                                    <option @if (old('nationality') === 'nepalese') selected @endif value="nepalese">Nepalese</option>
                                    <option @if (old('nationality') === 'new') selected @endif value="new zealander">New Zealander</option>
                                    <option @if (old('nationality') === 'ni') selected @endif value="ni-vanuatu">Ni-Vanuatu</option>
                                    <option @if (old('nationality') === 'nicaraguan') selected @endif value="nicaraguan">Nicaraguan</option>
                                    <option @if (old('nationality') === 'nigerien') selected @endif value="nigerien">Nigerien</option>
                                    <option @if (old('nationality') === 'north') selected @endif value="north korean">North Korean</option>
                                    <option @if (old('nationality') === 'northern') selected @endif value="northern irish">Northern Irish</option>
                                    <option @if (old('nationality') === 'norwegian') selected @endif value="norwegian">Norwegian</option>
                                    <option @if (old('nationality') === 'omani') selected @endif value="omani">Omani</option>
                                    <option @if (old('nationality') === 'pakistani') selected @endif value="pakistani">Pakistani</option>
                                    <option @if (old('nationality') === 'palauan') selected @endif value="palauan">Palauan</option>
                                    <option @if (old('nationality') === 'panamanian') selected @endif value="panamanian">Panamanian</option>
                                    <option @if (old('nationality') === 'papua_new_guinean') selected @endif value="papua_new_guinean">Papua New Guinean</option>
                                    <option @if (old('nationality') === 'paraguayan') selected @endif value="paraguayan">Paraguayan</option>
                                    <option @if (old('nationality') === 'peruvian') selected @endif value="peruvian">Peruvian</option>
                                    <option @if (old('nationality') === 'polish') selected @endif value="polish">Polish</option>
                                    <option @if (old('nationality') === 'portuguese') selected @endif value="portuguese">Portuguese</option>
                                    <option @if (old('nationality') === 'qatari') selected @endif value="qatari">Qatari</option>
                                    <option @if (old('nationality') === 'romanian') selected @endif value="romanian">Romanian</option>
                                    <option @if (old('nationality') === 'russian') selected @endif value="russian">Russian</option>
                                    <option @if (old('nationality') === 'rwandan') selected @endif value="rwandan">Rwandan</option>
                                    <option @if (old('nationality') === 'saint') selected @endif value="saint lucian">Saint Lucian</option>
                                    <option @if (old('nationality') === 'salvadoran') selected @endif value="salvadoran">Salvadoran</option>
                                    <option @if (old('nationality') === 'samoan') selected @endif value="samoan">Samoan</option>
                                    <option @if (old('nationality') === 'san') selected @endif value="san marinese">San Marinese</option>
                                    <option @if (old('nationality') === 'sao') selected @endif value="sao tomean">Sao Tomean</option>
                                    <option @if (old('nationality') === 'saudi') selected @endif value="saudi">Saudi</option>
                                    <option @if (old('nationality') === 'scottish') selected @endif value="scottish">Scottish</option>
                                    <option @if (old('nationality') === 'senegalese') selected @endif value="senegalese">Senegalese</option>
                                    <option @if (old('nationality') === 'serbian') selected @endif value="serbian">Serbian</option>
                                    <option @if (old('nationality') === 'seychellois') selected @endif value="seychellois">Seychellois</option>
                                    <option @if (old('nationality') === 'sierra') selected @endif value="sierra leonean">Sierra Leonean</option>
                                    <option @if (old('nationality') === 'singaporean') selected @endif value="singaporean">Singaporean</option>
                                    <option @if (old('nationality') === 'slovakian') selected @endif value="slovakian">Slovakian</option>
                                    <option @if (old('nationality') === 'slovenian') selected @endif value="slovenian">Slovenian</option>
                                    <option @if (old('nationality') === 'solomon_islander') selected @endif value="solomon_islander">Solomon Islander</option>
                                    <option @if (old('nationality') === 'somali') selected @endif value="somali">Somali</option>
                                    <option @if (old('nationality') === 'south') selected @endif value="south african">South African</option>
                                    <option @if (old('nationality') === 'south') selected @endif value="south korean">South Korean</option>
                                    <option @if (old('nationality') === 'spanish') selected @endif value="spanish">Spanish</option>
                                    <option @if (old('nationality') === 'sri') selected @endif value="sri lankan">Sri Lankan</option>
                                    <option @if (old('nationality') === 'sudanese') selected @endif value="sudanese">Sudanese</option>
                                    <option @if (old('nationality') === 'surinamer') selected @endif value="surinamer">Surinamer</option>
                                    <option @if (old('nationality') === 'swazi') selected @endif value="swazi">Swazi</option>
                                    <option @if (old('nationality') === 'swedish') selected @endif value="swedish">Swedish</option>
                                    <option @if (old('nationality') === 'swiss') selected @endif value="swiss">Swiss</option>
                                    <option @if (old('nationality') === 'syrian') selected @endif value="syrian">Syrian</option>
                                    <option @if (old('nationality') === 'taiwanese') selected @endif value="taiwanese">Taiwanese</option>
                                    <option @if (old('nationality') === 'tajik') selected @endif value="tajik">Tajik</option>
                                    <option @if (old('nationality') === 'tanzanian') selected @endif value="tanzanian">Tanzanian</option>
                                    <option @if (old('nationality') === 'thai') selected @endif value="thai">Thai</option>
                                    <option @if (old('nationality') === 'togolese') selected @endif value="togolese">Togolese</option>
                                    <option @if (old('nationality') === 'tongan') selected @endif value="tongan">Tongan</option>
                                    <option @if (old('nationality') === 'trinidadian_tobagonian') selected @endif value="trinidadian_tobagonian"> Trinidadian or Tobagonian</option>
                                    <option @if (old('nationality') === 'tunisian') selected @endif value="tunisian">Tunisian</option>
                                    <option @if (old('nationality') === 'turkish') selected @endif value="turkish">Turkish</option>
                                    <option @if (old('nationality') === 'tuvaluan') selected @endif value="tuvaluan">Tuvaluan</option>
                                    <option @if (old('nationality') === 'ugandan') selected @endif value="ugandan">Ugandan</option>
                                    <option @if (old('nationality') === 'ukrainian') selected @endif value="ukrainian">Ukrainian</option>
                                    <option @if (old('nationality') === 'uruguayan') selected @endif value="uruguayan">Uruguayan</option>
                                    <option @if (old('nationality') === 'uzbekistani') selected @endif value="uzbekistani">Uzbekistani</option>
                                    <option @if (old('nationality') === 'venezuelan') selected @endif value="venezuelan">Venezuelan</option>
                                    <option @if (old('nationality') === 'vietnamese') selected @endif value="vietnamese">Vietnamese</option>
                                    <option @if (old('nationality') === 'welsh') selected @endif value="welsh">Welsh</option>
                                    <option @if (old('nationality') === 'yemenite') selected @endif value="yemenite">Yemenite</option>
                                    <option @if (old('nationality') === 'zambian') selected @endif value="zambian">Zambian</option>
                                    <option @if (old('nationality') === 'zimbabwean') selected @endif value="zimbabwean">Zimbabwean</option>
                                </select>
                                @if ($errors->has('nationality'))
                                    <span class="invalid-response text-danger" role="alert">
                                        <strong>
                                            {{ $errors->first('nationality') }}
                                        </strong>
                                    </span>
                                @endif
                            </div>

                            {{-- Additional Information --}}
                            <div class="form-group form-group-last">
                                <label for="additional_details">Additional Details</label>
                                <textarea class="form-control" id="additional_details" name="additional_details" rows="3"></textarea>
                            </div>

                        </div>
                    </div>



                    {{-- ----------------- --}}
                    {{--    Visa Details   --}}
                    {{-- ----------------- --}}
                    <div class="kt-wizard-v3__content" data-ktwizard-type="step-content">
                        <div class="kt-heading kt-heading--md">Visa Details</div>
                        <div class="kt-separator kt-separator--height-xs"></div>
                        <div class="kt-form__section kt-form__section--first">


                            <div class="form-group">
                                <div class="kt-checkbox-list">
                                    <label class="kt-checkbox kt-checkbox--brand">
                                        <input type="checkbox" checked name="has_visa" data-addonselection="visa"> Driver have visa?
                                        <span></span>
                                    </label>
                                </div>
                                <span class="form-text text-muted">Do driver already have a visa?</span>
                            </div>

                            <div class="kt-separator kt-separator--space-sm  kt-separator--border-solid"></div>

                            {{-- # DRIVER HAVE VISA --}}
                            <div data-addonselection="visa" data-addonselection-when="1">
                                <div class="kt-section">
                                    <div class="kt-section__info kt-font-info">
                                        <i class="flaticon2-information"></i>
                                        Please enter the details of visa.
                                    </div>
                                </div>
                                <div class="kt-section__content kt-section__content--border">

                                    {{-- Emirates ID --}}
                                    <div class="form-group">
                                        <label>Emirates ID <span class="text-danger">*</span></label>
                                        <input type="text" required autocomplete="off" name="emirates_id_no" class="form-control @error('emirates_id_no') is-invalid @enderror" placeholder="Emirates ID" value="{{ old('emirates_id_no') }}">
                                        @if ($errors->has('emirates_id_no'))
                                            <span class="invalid-response text-danger" role="alert">
                                                <strong>
                                                    {{ $errors->first('emirates_id_no') }}
                                                </strong>
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Emirates ID Images --}}
                                    <div class="form-group">
                                        <label>Uplaod Emirates ID Pictures <span class="text-danger">*</span></label>
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <div class="kt-uppy kr-uppy" uppy-size="20" uppy-max="1" uppy-min="1" uppy-label="Front Picture" uppy-input="emirates_id_pictures_front"></div>
                                                <span class="form-text text-muted">Max file size is 20MB and max number of files is 1.</span>
                                                @if ($errors->has('emirates_id_pictures_front'))
                                                    <span class="invalid-response text-danger" role="alert">
                                                        <strong>
                                                            {{ $errors->first('emirates_id_pictures_front') }}
                                                        </strong>
                                                    </span>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="kt-uppy kr-uppy" uppy-size="20" uppy-max="1" uppy-min="1" uppy-label="Back Picture" uppy-input="emirates_id_pictures_back"></div>
                                                <span class="form-text text-muted">Max file size is 20MB and max number of files is 1.</span>
                                                @if ($errors->has('emirates_id_pictures_back'))
                                                    <span class="invalid-response text-danger" role="alert">
                                                        <strong>
                                                            {{ $errors->first('emirates_id_pictures_back') }}
                                                        </strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Emirates ID Expiry --}}
                                    <div class="form-group">
                                        <label>Emirates ID Expiry <span class="text-danger">*</span></label>
                                        <input type="text" required readonly name="emirates_id_expiry" data-state="date" class="kr-datepicker form-control @error('emirates_id_expiry') is-invalid @enderror" data-default="{{ old('emirates_id_expiry') }}">
                                        @if ($errors->has('emirates_id_expiry'))
                                            <span class="invalid-response text-danger" role="alert">
                                                <strong>
                                                    {{ $errors->first('emirates_id_expiry') }}
                                                </strong>
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Visa Images --}}
                                    <div class="form-group">
                                        <label>Visa Picture <span class="text-danger">*</span></label>
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <div class="kt-uppy kr-uppy" uppy-size="20" uppy-max="1" uppy-min="1" uppy-label="VISA Picture" uppy-input="visa_pictures_front"></div>
                                                <span class="form-text text-muted">Max file size is 20MB and max number of files is 1.</span>
                                                @if ($errors->has('visa_pictures_front'))
                                                    <span class="invalid-response text-danger" role="alert">
                                                        <strong>
                                                            {{ $errors->first('visa_pictures_front') }}
                                                        </strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Visa Expiry --}}
                                    <div class="form-group">
                                        <label>Visa Expiry <span class="text-danger">*</span></label>
                                        <input type="text" required readonly name="visa_expiry" data-state="date" class="kr-datepicker form-control @error('visa_expiry') is-invalid @enderror" data-default="{{ old('visa_expiry') }}">
                                        @if ($errors->has('visa_expiry'))
                                            <span class="invalid-response text-danger" role="alert">
                                                <strong>
                                                    {{ $errors->first('visa_expiry') }}
                                                </strong>
                                            </span>
                                        @endif
                                    </div>

                                </div>
                            </div>

                            {{-- # DRIVER DOESN'T HAVE VISA --}}
                            <div data-addonselection="visa" data-addonselection-when="0">
                                <div class="kt-section">
                                    <div class="kt-section__info kt-font-info">
                                        <i class="flaticon2-information"></i>
                                        Please enter the details of visa. This will create an addon of visa
                                    </div>
                                </div>
                                <div class="kt-section__content kt-section__content--border">

                                    {{-- SettingID --}}
                                    <input type="hidden" name="addon[Visa][id]" value="{{ $addon_settings['Visa']['_id'] }}">

                                    {{-- Price --}}
                                    <div class="form-group">
                                        <label>Price </label>
                                        <input type="number" step="0.001" autocomplete="off" required name="addon[Visa][price]" class="form-control @error('addon[Visa][price]') is-invalid @enderror" value="{{old('addon[Visa][price]') ?? $addon_settings['Visa']['amount']}}">
                                        @if ($errors->has('addon[Visa][price]'))
                                            <span class="invalid-response text-danger" role="alert">
                                                <strong>
                                                    {{ $errors->first('addon[Visa][price]') }}
                                                </strong>
                                            </span>
                                        @endif
                                    </div>

                                    <div class="form-group">

                                        <label class="kt-checkbox kt-checkbox--brand">
                                            <input type="checkbox" name="addon[Visa][override_setting]" data-addonselection="visa_setting"> Override default settings?
                                            <span></span>
                                        </label>

                                        <div data-addonselection="visa_setting" data-addonselection-when="1">
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">This will override addon expense types against this addon</span>
                                                <a href="" onclick="DRIVER_MODULE.addon_module.reset_table(event, '{{ $addon_settings['Visa']['_id'] }}', this);">Reset table</a>
                                            </div>
                                            <div class="addon-setting-container border" >
                                                <table class="table border-0 table-bordered table-hover table-checkable datatable datatable-types" data-addon-id="{{ $addon_settings['Visa']['_id'] }}">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Title</th>
                                                            <th>Display Title</th>
                                                            <th>Default Amount?</th>
                                                            <th>Charge from source?</th>
                                                            <th></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                            <div class="border border-top-0 py-2 px-3">
                                                <button type="button" class="btn btn-outline-info btn-elevate btn-square btn-sm" onclick="DRIVER_MODULE.addon_module.append_row('{{ $addon_settings['Visa']['_id'] }}');">
                                                    <i class="flaticon2-plus-1"></i>
                                                    Add Row
                                                </button>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>


                        </div>
                    </div>


                    {{-- ----------------- --}}
                    {{--  License Details  --}}
                    {{-- ----------------- --}}
                    <div class="kt-wizard-v3__content" data-ktwizard-type="step-content">
                        <div class="kt-heading kt-heading--md">License Details</div>
                        <div class="kt-separator kt-separator--height-xs"></div>
                        <div class="kt-form__section kt-form__section--first">

                            <div class="form-group">
                                <div class="kt-checkbox-list">
                                    <label class="kt-checkbox kt-checkbox--brand">
                                        <input type="checkbox" checked name="has_license" data-addonselection="license"> Driver have license?
                                        <span></span>
                                    </label>
                                </div>
                                <span class="form-text text-muted">Do driver already have a license?</span>
                            </div>

                            <div class="kt-separator kt-separator--space-sm  kt-separator--border-solid"></div>

                            {{-- # DRIVER HAVE LICENSE --}}
                            <div data-addonselection="license" data-addonselection-when="1">
                                <div class="kt-section">
                                    <div class="kt-section__info kt-font-info">
                                        <i class="flaticon2-information"></i>
                                        Please enter the details of license.
                                    </div>
                                </div>
                                <div class="kt-section__content kt-section__content--border">

                                    {{-- License Number --}}
                                    <div class="form-group">
                                        <label>License Number <span class="text-danger">*</span></label>
                                        <input type="text" required autocomplete="off" name="liscence_number" class="form-control @error('liscence_number') is-invalid @enderror" placeholder="Enter License" value="{{ old('liscence_number') }}">
                                        @if ($errors->has('liscence_number'))
                                            <span class="invalid-response text-danger" role="alert">
                                                <strong>
                                                    {{ $errors->first('liscence_number') }}
                                                </strong>
                                            </span>
                                        @endif
                                    </div>

                                    {{-- License Images --}}
                                    <div class="form-group">
                                        <label>Uplaod License Pictures <span class="text-danger">*</span></label>
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <div class="kt-uppy kr-uppy" uppy-size="20" uppy-max="1" uppy-min="1" uppy-label="Front Picture" uppy-input="liscence_pictures_front"></div>
                                                <span class="form-text text-muted">Max file size is 20MB and max number of files is 1.</span>
                                                @if ($errors->has('liscence_pictures_front'))
                                                    <span class="invalid-response text-danger" role="alert">
                                                        <strong>
                                                            {{ $errors->first('liscence_pictures_front') }}
                                                        </strong>
                                                    </span>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="kt-uppy kr-uppy" uppy-size="20" uppy-max="1" uppy-min="1" uppy-label="Back Picture" uppy-input="liscence_pictures_back"></div>
                                                <span class="form-text text-muted">Max file size is 20MB and max number of files is 1.</span>
                                                @if ($errors->has('liscence_pictures_back'))
                                                    <span class="invalid-response text-danger" role="alert">
                                                        <strong>
                                                            {{ $errors->first('liscence_pictures_back') }}
                                                        </strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    {{-- License Expiry --}}
                                    <div class="form-group">
                                        <label>License Expiry <span class="text-danger">*</span></label>
                                        <input type="text" required readonly name="liscence_expiry" data-state="date" class="kr-datepicker form-control @error('liscence_expiry') is-invalid @enderror" data-default="{{ old('liscence_expiry') }}">
                                        @if ($errors->has('liscence_expiry'))
                                            <span class="invalid-response text-danger" role="alert">
                                                <strong>
                                                    {{ $errors->first('liscence_expiry') }}
                                                </strong>
                                            </span>
                                        @endif

                                    </div>

                                </div>
                            </div>

                            {{-- # DRIVER DOESN'T HAVE LICENSE --}}
                            <div data-addonselection="license" data-addonselection-when="0">
                                <div class="kt-section">
                                    <div class="kt-section__info kt-font-info">
                                        <i class="flaticon2-information"></i>
                                        Please enter the details of license. This will create an addon of license
                                    </div>
                                </div>
                                <div class="kt-section__content kt-section__content--border">

                                    {{-- License Type {Dubai/Sharjah} --}}
                                    <div class="form-group">

                                        <div class="kt-radio-list d-flex">
                                            <label class="kt-radio kt-radio--bold kt-radio--brand">
                                                <input type="radio" name="license_selection" value="dubai" checked data-addonselection="license_selection"> Dubai
                                                <span></span>
                                            </label>
                                            <label class="kt-radio kt-radio--bold kt-radio--brand ml-4">
                                                <input type="radio" name="license_selection" value="sharjah" data-addonselection="license_selection"> Sharjah
                                                <span></span>
                                            </label>
                                        </div>
                                        <span class="form-text text-muted">Please select the license location?</span>

                                    </div>

                                    <div data-addonselection="license_selection" data-addonselection-when="dubai">

                                        {{-- SettingID --}}
                                        <input type="hidden" name="addon[Driving License Dubai][id]" value="{{ $addon_settings['Driving License Dubai']['_id'] }}">

                                        {{-- Price --}}
                                        <div class="form-group">
                                            <label>Price </label>
                                            <input type="number" step="0.001" autocomplete="off" required name="addon[Driving License Dubai][price]" class="form-control @error('addon[Driving License Dubai][price]') is-invalid @enderror" value="{{old('addon[Driving License Dubai][price]') ?? $addon_settings['Driving License Dubai']['amount']}}">
                                            @if ($errors->has('addon[Driving License Dubai][price]'))
                                                <span class="invalid-response text-danger" role="alert">
                                                    <strong>
                                                        {{ $errors->first('addon[Driving License Dubai][price]') }}
                                                    </strong>
                                                </span>
                                            @endif
                                        </div>

                                        {{-- Addon Settings --}}
                                        <div class="form-group">

                                            <label class="kt-checkbox kt-checkbox--brand">
                                                <input type="checkbox" name="addon[Driving License Dubai][override_setting]" data-addonselection="license_dubai_setting"> Override default settings?
                                                <span></span>
                                            </label>

                                            <div data-addonselection="license_dubai_setting" data-addonselection-when="1">
                                                <div class="d-flex justify-content-between">
                                                    <span class="text-muted">This will override addon expense types against this addon</span>
                                                    <a href="" onclick="DRIVER_MODULE.addon_module.reset_table(event, '{{ $addon_settings['Driving License Dubai']['_id'] }}', this);">Reset table</a>
                                                </div>
                                                <div class="addon-setting-container border" >
                                                    <table class="table border-0 table-bordered table-hover table-checkable datatable datatable-types" data-addon-id="{{ $addon_settings['Driving License Dubai']['_id'] }}">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>Title</th>
                                                                <th>Display Title</th>
                                                                <th>Default Amount?</th>
                                                                <th>Charge from source?</th>
                                                                <th></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody></tbody>
                                                    </table>
                                                </div>
                                                <div class="border border-top-0 py-2 px-3">
                                                    <button type="button" class="btn btn-outline-info btn-elevate btn-square btn-sm" onclick="DRIVER_MODULE.addon_module.append_row('{{ $addon_settings['Driving License Dubai']['_id'] }}');">
                                                        <i class="flaticon2-plus-1"></i>
                                                        Add Row
                                                    </button>
                                                </div>
                                            </div>

                                        </div>

                                    </div>

                                    <div data-addonselection="license_selection" data-addonselection-when="sharjah">

                                        {{-- SettingID --}}
                                        <input type="hidden" name="addon[Driving License Sharjah][id]" value="{{ $addon_settings['Driving License Sharjah']['_id'] }}">

                                        {{-- Price --}}
                                        <div class="form-group">
                                            <label>Price </label>
                                            <input type="number" step="0.001" autocomplete="off" required name="addon[Driving License Sharjah][price]" class="form-control @error('addon[Driving License Dubai][price]') is-invalid @enderror" value="{{old('addon[Driving License Dubai][price]') ?? $addon_settings['Driving License Dubai']['amount']}}">
                                            @if ($errors->has('addon[Driving License Sharjah][price]'))
                                                <span class="invalid-response text-danger" role="alert">
                                                    <strong>
                                                        {{ $errors->first('addon[Driving License Sharjah][price]') }}
                                                    </strong>
                                                </span>
                                            @endif
                                        </div>

                                        {{-- Addon Settings --}}
                                        <div class="form-group">

                                            <label class="kt-checkbox kt-checkbox--brand">
                                                <input type="checkbox" name="addon[Driving License Sharjah][override_setting]" data-addonselection="license_sharjah_setting"> Override default settings?
                                                <span></span>
                                            </label>

                                            <div data-addonselection="license_sharjah_setting" data-addonselection-when="1">
                                                <div class="d-flex justify-content-between">
                                                    <span class="text-muted">This will override addon expense types against this addon</span>
                                                    <a href="" onclick="DRIVER_MODULE.addon_module.reset_table(event, '{{ $addon_settings['Driving License Sharjah']['_id'] }}', this);">Reset table</a>
                                                </div>
                                                <div class="addon-setting-container border" >
                                                    <table class="table border-0 table-bordered table-hover table-checkable datatable datatable-types" data-addon-id="{{ $addon_settings['Driving License Sharjah']['_id'] }}">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>Title</th>
                                                                <th>Display Title</th>
                                                                <th>Default Amount?</th>
                                                                <th>Charge from source?</th>
                                                                <th></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody></tbody>
                                                    </table>
                                                </div>
                                                <div class="border border-top-0 py-2 px-3">
                                                    <button type="button" class="btn btn-outline-info btn-elevate btn-square btn-sm" onclick="DRIVER_MODULE.addon_module.append_row('{{ $addon_settings['Driving License Sharjah']['_id'] }}');">
                                                        <i class="flaticon2-plus-1"></i>
                                                        Add Row
                                                    </button>
                                                </div>
                                            </div>

                                        </div>

                                    </div>

                                </div>
                            </div>


                        </div>
                    </div>


                    {{-- ----------------- --}}
                    {{--    RTA Details    --}}
                    {{-- ----------------- --}}
                    <div class="kt-wizard-v3__content" data-ktwizard-type="step-content">
                        <div class="kt-heading kt-heading--md">RTA Details</div>
                        <div class="kt-separator kt-separator--height-xs"></div>
                        <div class="kt-form__section kt-form__section--first">

                            <div class="form-group">
                                <div class="kt-checkbox-list">
                                    <label class="kt-checkbox kt-checkbox--brand">
                                        <input type="checkbox" checked name="has_rta" data-addonselection="rta"> Driver have RTA?
                                        <span></span>
                                    </label>
                                </div>
                                <span class="form-text text-muted">Do driver already have a RTA?</span>
                            </div>

                            <div class="kt-separator kt-separator--space-sm  kt-separator--border-solid"></div>

                            {{-- # DRIVER HAVE RTA --}}
                            <div data-addonselection="rta" data-addonselection-when="1">
                                <div class="kt-section">
                                    <div class="kt-section__info kt-font-info">
                                        <i class="flaticon2-information"></i>
                                        Please enter the details of RTA.
                                    </div>
                                </div>
                                <div class="kt-section__content kt-section__content--border">

                                    {{-- RTA PERMIT Number --}}
                                    <div class="form-group">
                                        <label>RTA Permit Number <span class="text-danger">*</span></label>
                                        <input type="text" required autocomplete="off" name="rta_permit_number" class="form-control @error('passport_number') is-invalid @enderror" placeholder="RTA Permit Number" value="{{ old('rta_permit_number') }}">
                                        @if ($errors->has('rta_permit_number'))
                                            <span class="invalid-response text-danger" role="alert">
                                                <strong>
                                                    {{ $errors->first('rta_permit_number') }}
                                                </strong>
                                            </span>
                                        @endif
                                    </div>

                                    {{-- RTA PERMIT Expiry --}}
                                    <div class="form-group">
                                        <label>RTA Permit Expiry <span class="text-danger">*</span></label>
                                        <input type="text" required readonly name="rta_permit_expiry" data-state="date" class="kr-datepicker form-control @error('rta_permit_expiry') is-invalid @enderror" data-default="{{ old('rta_permit_expiry') }}">
                                        @if ($errors->has('rta_permit_expiry'))
                                            <span class="invalid-response text-danger" role="alert">
                                                <strong>
                                                    {{ $errors->first('rta_permit') }}
                                                </strong>
                                            </span>
                                        @endif

                                    </div>

                                    {{-- RTA PERMIT IMAGES --}}
                                    <div class="form-group">
                                        <label>RTA Permit Pictures <span class="text-danger">*</span></label>
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <div class="kt-uppy kr-uppy" uppy-size="20" uppy-max="1" uppy-min="1" uppy-label="Front Picture" uppy-input="rta_permit_pictures_front"></div>
                                                <span class="form-text text-muted">Max file size is 20MB and max number of files is 1.</span>
                                                @if ($errors->has('rta_permit_pictures_front'))
                                                    <span class="invalid-response text-danger" role="alert">
                                                        <strong>
                                                            {{ $errors->first('rta_permit_pictures_front') }}
                                                        </strong>
                                                    </span>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="kt-uppy kr-uppy" uppy-size="20" uppy-max="1" uppy-min="1" uppy-label="Back Picture" uppy-input="rta_permit_pictures_back"></div>
                                                <span class="form-text text-muted">Max file size is 20MB and max number of files is 1.</span>
                                                @if ($errors->has('rta_permit_pictures_back'))
                                                    <span class="invalid-response text-danger" role="alert">
                                                        <strong>
                                                            {{ $errors->first('rta_permit_pictures_back') }}
                                                        </strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            {{-- # DRIVER DOESN'T HAVE RTA --}}
                            <div data-addonselection="rta" data-addonselection-when="0">
                                <div class="kt-section">
                                    <div class="kt-section__info kt-font-info">
                                        <i class="flaticon2-information"></i>
                                        Please enter the details of RTA. This will create an addon of RTA
                                    </div>
                                </div>
                                <div class="kt-section__content kt-section__content--border">

                                    {{-- SettingID --}}
                                    <input type="hidden" name="addon[RTA][id]" value="{{ $addon_settings['RTA']['_id'] }}">

                                    {{-- Price --}}
                                    <div class="form-group">
                                        <label>Price </label>
                                        <input type="number" step="0.001" autocomplete="off" required name="addon[RTA][price]" class="form-control @error('addon[RTA][price]') is-invalid @enderror" value="{{old('addon[RTA][price]') ?? $addon_settings['RTA']['amount']}}">
                                        @if ($errors->has('addon[RTA][price]'))
                                            <span class="invalid-response text-danger" role="alert">
                                                <strong>
                                                    {{ $errors->first('addon[RTA][price]') }}
                                                </strong>
                                            </span>
                                        @endif
                                    </div>

                                    <div class="form-group">

                                        <label class="kt-checkbox kt-checkbox--brand">
                                            <input type="checkbox" name="addon[RTA][override_setting]" data-addonselection="rta_setting"> Override default settings?
                                            <span></span>
                                        </label>

                                        <div data-addonselection="rta_setting" data-addonselection-when="1">
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">This will override addon expense types against this addon</span>
                                                <a href="" onclick="DRIVER_MODULE.addon_module.reset_table(event, '{{ $addon_settings['RTA']['_id'] }}', this);">Reset table</a>
                                            </div>
                                            <div class="addon-setting-container border" >
                                                <table class="table border-0 table-bordered table-hover table-checkable datatable datatable-types" data-addon-id="{{ $addon_settings['RTA']['_id'] }}">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Title</th>
                                                            <th>Display Title</th>
                                                            <th>Default Amount?</th>
                                                            <th>Charge from source?</th>
                                                            <th></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                            <div class="border border-top-0 py-2 px-3">
                                                <button type="button" class="btn btn-outline-info btn-elevate btn-square btn-sm" onclick="DRIVER_MODULE.addon_module.append_row('{{ $addon_settings['RTA']['_id'] }}');">
                                                    <i class="flaticon2-plus-1"></i>
                                                    Add Row
                                                </button>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>


                        </div>
                    </div>

                    {{-- ----------------- --}}
                    {{--      Finalize     --}}
                    {{-- ----------------- --}}
                    <div class="kt-wizard-v3__content" data-ktwizard-type="step-content">
                        <div class="kt-heading kt-heading--md">Preview Details</div>
                        <div class="kt-separator kt-separator--height-xs"></div>
                        <div class="kt-form__section kt-form__section--first">

                            <div class="preview-data-container">{{-- Appended by JS --}}</div>


                        </div>
                    </div>


                    {{-- ----------------- --}}
                    {{--      ACTIONS      --}}
                    {{-- ----------------- --}}
                    <div class="kt-form__actions">
                        <div>
                            <button type="button" class="btn btn-outline-brand btn-md btn-tall btn-wide btn-bold btn-upper" data-custom-ktwizard-type="action-prev">
                                Previous
                            </button>
                        </div>
                        <div>
                            <button type="button" class="btn btn-brand btn-md btn-tall btn-wide btn-bold btn-upper" data-custom-ktwizard-type="action-submit">
                                Submit
                            </button>
                        </div>
                        <div class="d-flex flex-column">
                            <button type="button" class="btn btn-brand btn-md btn-tall btn-wide btn-bold btn-upper px-5" data-custom-ktwizard-type="action-next">
                                Next Step
                            </button>
                            <div data-custom-ktwizard-type="action-continue" style="display: none;">
                                <div class="d-flex justify-content-between align-items-center mt-2">

                                    <a href="#" class="kt-link kt-link--info">
                                        Continue Anyway
                                    </a>
                                    <span class="kt-font-info" data-toggle="kt-tooltip" data-skin="dark" title="This will skip validation. You need to provide empty fields later.">
                                        <i class="flaticon-info"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>


                </form>
                <!-- --------------------------- -->
                <!--        end: FORM            -->
                <!-- --------------------------- -->


            </div>

        </div>
    </div>

    <!--end::Portlet-->
@endsection

@section('foot')

    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/intlTelInput.min.js" kr-ajax-head type="text/javascript"></script>

    {{------------------------------------------------------------------------------
        HANDLEBARS TEMPLAATES
    --------------------------------------------------------------------------------}}

    {{-- ADD ROW TEMPLATE - Types --}}
    @include('Tenant.drivers.handlebars_templates.add_types')

    {{-- ----------------------------------------------------------------------------
                                SCRIPTS (use in current page)
    ------------------------------------------------------------------------------ --}}

    <script kr-ajax-head type="text/javascript">
        var DRIVER_MODULE = {
            container: '#kt-portlet__create-driver',

            addon_module: function(){

                var AddonSettings = @json($addon_types);

                var getSetting = function(id = null){
                    return AddonSettings.find(x=>x._id === id) || null;
                }

                var getTable = function(id = null){

                    var containers = null;
                    if(id){
                        containers = DRIVER_MODULE.container+' [data-addon-id="'+id+'"]';
                    }
                    else{
                        containers = DRIVER_MODULE.container+' [data-addon-id]';
                    }

                    return containers;

                }

                var calculate_subtotal = function(){

                }

                var update_indices=function(){
                    $(getTable()).each(function(tableIndex, tableEl){

                        $(tableEl).find('tbody tr').each(function(rowIndex, elem){

                            /* update names */
                            $(this).find('[data-name]').attr('name', function(index, attr){
                                var name = $(this).attr('data-name');
                                var key = $(this).attr('data-key');
                                $(this).attr('name', key+'['+rowIndex+']['+name+']');
                            });

                            /* update SR # */
                            $(this).find('.srno').text(rowIndex+1);
                        });

                    })

                }

                return {
                    calculate_subtotal:function(is_minimal=false){
                        /* clear the errors */
                        DRIVER_MODULE.addon_module.errors.clear();

                        /* check for error rows (which have no part selected) */
                        DRIVER_MODULE.addon_module.validate_rows();

                        if(!is_minimal) update_indices();

                        /* calculate the amount through each loop*/
                        calculate_subtotal();

                    },

                    append_row:function(id, item=null){

                        var template = $('#handlebars-addrow-types').html();

                        var insertAfter=null;
                        if(typeof arguments[2]!=="undefined" && arguments[2])insertAfter=arguments[2];

                        // Compile the template data into a function
                        var templateScript = Handlebars.compile(template);
                        var setting = getSetting(id);

                        var index = $(getTable(id)+' tbody tr').length;
                        var context = {
                            index:index,
                            append:false
                        }
                        if(item){

                            // If display title not found, make it title
                            if(!item.display_title) item.display_title = item.title;

                            context.item=item;
                            context.append=true;
                        }
                        if(setting){
                            context.addonType = setting.title;
                        }


                        var html = templateScript(context);

                        if(insertAfter) insertAfter.after(html);
                        else $(getTable(id)+' tbody').append(html);


                        // to refresh select2
                        kingriders.Plugins.refresh_plugins();

                    },

                    delete_row:function(self){
                        $(self).parents('tr').remove();

                        var id = $(self).parents('[data-addon-id]').attr('data-addon-id');

                        /* check if no rows present */
                        if($(getTable(id)+' tbody tr').length==0){
                            /* append a blank row */
                            DRIVER_MODULE.addon_module.append_row(id);
                        }

                        this.calculate_subtotal();

                    },

                    reset_table: function(event, id, el){
                        event.preventDefault();
                        var self = this;

                        swal.fire({
                            title: 'Are you sure?',
                            text: "This will populate the table with default setting!",
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, reset it!',
                            scrollbarPadding: false,
                            allowOutsideClick: function() {
                                return !swal.isLoading()
                            }
                        })
                        .then(function(result) {
                            if (result.value) {

                                var setting = getSetting(id);
                                if(setting){

                                    /* empty table */
                                    if($(getTable(id)).length==1){
                                        $(getTable(id)+' tbody').html('');
                                    }

                                    /* Append rows */
                                    setting.types.forEach(function(type) {
                                        self.append_row(setting._id, type);
                                    });
                                }

                            }
                        });

                    },

                    validate_rows:function(){
                        return true;

                        var is_validate=true;

                        $(DRIVER_MODULE.container+' .invalid-feedback').removeAttr('style');

                        var is_valid_global = true;
                        /* Validate title */
                        var title = $(DRIVER_MODULE.container+' [name="title"]').val();
                        title && (title=title.trim());
                        var source = $(DRIVER_MODULE.container+' [name="source"]:checked').val();
                        source && (source=source.trim());

                        var exists = false;
                        if($(DRIVER_MODULE.container+' [name=addon_setting_id]').length > 0){
                            var cuurentId = $(DRIVER_MODULE.addon_module.container+' [name=addon_setting_id]').val();
                            exists = DRIVER_MODULE.addon_module.settings().some(x=> x.source_type === source && x.title.trim().toLowerCase() === title.toLowerCase() && x._id != cuurentId);
                        }
                        else{
                            exists = DRIVER_MODULE.addon_module.settings().some(x=> x.source_type === source && x.title.trim().toLowerCase() === title.toLowerCase());
                        }

                        if(!!title && exists){
                            is_valid_global=false;

                            DRIVER_MODULE.addon_module.errors.make_field($(DRIVER_MODULE.addon_module.container+' [name="title"]'), "Title already exists with this name");
                        }

                        if(!is_valid_global){

                            /* global validation, if any rows found invalid, */
                            is_validate=false;
                        }


                        /* reset rows (remove error class) */
                        $(DRIVER_MODULE.addon_module.container+' .datatable > tbody > tr').removeClass('row__is-invalid');

                        /* loop through all rows */
                        $(DRIVER_MODULE.addon_module.container+' .datatable-types > tbody > tr').each(function(index, el){
                            var is_valid=true;

                            var title = $(this).find('[data-name="title"]').val();
                            title && (title=title.trim());
                            if(!title)is_valid=false;

                            var display_title = $(this).find('[data-name="display_title"]').val();
                            display_title && (display_title=display_title.trim());
                            if(!display_title)is_valid=false;

                            if(!is_valid){
                                /* add error class to this row */
                                $(this).addClass('row__is-invalid');

                                /* global validation, if any rows found invalid, */
                                is_validate=false;
                            }
                        });

                        return is_validate;
                    },

                    errors:{
                        clear:function(){
                            $(DRIVER_MODULE.addon_module.container+' .error-alert').hide().find('.alert-text').html('');
                            $(DRIVER_MODULE.addon_module.container+' .invalid-feedback').removeAttr('style');
                        },
                        make:function(html){
                            $(DRIVER_MODULE.addon_module.container+' .error-alert').show().find('.alert-text').html(html);
                        },
                        make_field:function(el, html){
                            if(el.siblings('.invalid-feedback').length > 0) {
                                el.siblings('.invalid-feedback').show().html('<strong>'+html+'</strong>');
                            }
                        },
                    },

                    init: function(){
                        var self = this;

                        console.log("AddonSettings", AddonSettings);

                        // Load the table with default settings
                        AddonSettings.forEach(function(item) {
                            item.types.forEach(function(type) {
                                self.append_row(item._id, type);
                            });
                        });
                    }
                }
            }(),

            phone: function(){

                var init_phone = function(){

                    let input =  document.querySelector(DRIVER_MODULE.container+' [name=phone_number]');
                    if(!input) return;

                    const errorMsgEl = document.querySelector("#phone-error-msg");

                    // here, the index maps to the error code returned from getValidationError - see readme
                    const iti = window.intlTelInput(input, {
                        nationalMode: true,
                        utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js",
                        // hiddenInput: "full_phone",
                        separateDialCode: true,
                        initialCountry: "AE",
                        preferredCountries: ["ae", "pk"],

                        customPlaceholder: function(selectedCountryPlaceholder, selectedCountryData) {
                            return "e.g. " + selectedCountryPlaceholder;
                        },
                    });


                    const handleChange = function() {
                        let text = "";

                        // Reset everything
                        input.classList.remove('is-invalid');
                        $(DRIVER_MODULE.container + " [name=full_phone]").remove();

                        // Validate phone and appends errors
                        if (input.value) {
                            if(iti.isValidNumber()){
                                var n  = iti.getNumber();
                                text = "Valid number! Full format: <b>" + n + "</b>";
                                errorMsgEl.classList.remove('driver__phone-error');
                                $(input).after('<input type="hidden" name="full_phone" value="'+n+'" />');
                            }
                            else{
                                text = "Invalid number - please try again";
                                errorMsgEl.classList.add('driver__phone-error');
                                input.classList.add('is-invalid');
                            }
                        }
                        errorMsgEl.innerHTML = text;
                    };

                    // listen to "keyup", but also "change" to update when the user selects a country
                    input.addEventListener('change', handleChange);
                    input.addEventListener('keyup', handleChange);

                }
                return {
                    init: function(){
                        init_phone();
                    }
                }

            }(),

            wizard: function () {

                var wizardId = 'wizard_driver';
                var wizard_container = document.querySelector(this.container+' #'+wizardId);

                // Base elements
                var wizardEl;
                var formEl;
                var validator;
                var wizard;
                var continueAnyway = false;
                var driverId = null;

                var onChange = function(){

                    var currentStep = wizard.getStep();

                    console.log("CHANGED, STEP =", currentStep);


                    // If Final step, render preview
                    if( wizard.isLastStep() ){
                        PreviewDetails();
                    }

                    /* ------------------------------------- */
                    /*      Check if skip errors required    */
                    /*  : show/hide "continue anyway" button */
                    /* ------------------------------------- */
                    continueAnyway = false;

                    if( currentStep === 2 && $(DRIVER_MODULE.container + " [name=has_visa]").is(':checked') ){
                        continueAnyway = true;
                    }
                    if( currentStep === 3 && $(DRIVER_MODULE.container + " [name=has_license]").is(':checked') ){
                        continueAnyway = true;
                    }
                    if( currentStep === 4 && $(DRIVER_MODULE.container + " [name=has_rta]").is(':checked') ){
                        continueAnyway = true;
                    }


                    var el = $(DRIVER_MODULE.container + ' [data-custom-ktwizard-type="action-continue"]');
                    el.toggle(continueAnyway);


                    var stepTitle = DRIVER_MODULE.wizard.getStepTitle();
                    var name = 'is_'+stepTitle+'_skipped';
                    $(DRIVER_MODULE.container + ' form [name="'+name+'"]').remove();

                }


                // ---------------------------
                //  Render Preview Details
                // ---------------------------
                var PreviewDetails = function(){

                    // Fetch data from formData
                    var formData = new FormData(document.querySelector(DRIVER_MODULE.container+' form'));
                    var driver = kingriders.Utils.formdata_to_object(formData);

                    kingriders.Utils.isDebug() && console.log("DRIVER", driver);

                    var license_addon_name = '';
                    if(!driver.has_license && driver.license_selection === "dubai") license_addon_name = 'Driving License Dubai';
                    else if(!driver.has_license && driver.license_selection === "sharjah") license_addon_name = 'Driving License Sharjah';

                    var html = `
                        <!-- ------------------------ -->
                        <!--      BASIC DETAILS       -->
                        <!-- ------------------------ -->

                        <div class="kt-section">
                            <div class="kt-section__content  kt-section__content--border">

                                <div class="kt-section__title">
                                    Basic Details
                                </div>

                                <div class="form-group row form-group-marginless kt-margin-t-20">
                                    <label class="col-md-2 text-muted">Name:</label>
                                    <div class="col-md-4">
                                        <span class="kt-font-bold">${driver.name}</span>
                                    </div>
                                    <label class="col-md-2 text-muted">Email:</label>
                                    <div class="col-md-4">
                                        <span class="kt-font-bold">${driver.email}</span>
                                    </div>
                                </div>

                                <div class="form-group row form-group-marginless kt-margin-t-20">
                                    <label class="col-md-2 text-muted">Date of birth:</label>
                                    <div class="col-md-4">
                                        <span class="kt-font-bold">${driver.date_of_birth}</span>
                                    </div>
                                    <label class="col-md-2 text-muted">Type:</label>
                                    <div class="col-md-4">
                                        <span class="kt-font-bold">${driver.type.toUpperCase()}</span>
                                    </div>
                                </div>

                                <div class="form-group row form-group-marginless kt-margin-t-20">
                                    <label class="col-md-2 text-muted">Phone:</label>
                                    <div class="col-md-4">
                                        <span class="kt-font-bold">${driver.full_phone??""}</span>
                                    </div>
                                    <label class="col-md-2 text-muted">Profile Picture:</label>
                                    <div class="col-md-4">
                                        <div class="kt-font-bold">
                                            ${driver.profile_picture ?
                                            `
                                                <a target="_blank" href="${window.URL.createObjectURL(driver.profile_picture)}" class="kt-media">
                                                    <img src="${window.URL.createObjectURL(driver.profile_picture)}" />
                                                </a>
                                            `
                                            :
                                            `<span class="kt-font-danger">Not found</span>`
                                            }
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row form-group-marginless kt-margin-t-20">
                                    <label class="col-md-2 text-muted">Passport Collected:</label>
                                    <div class="col-md-4">
                                        <span class="kt-font-bold">${driver.is_pasport_collected ? 'Yes' : 'NO'}</span>
                                    </div>
                                    <label class="col-md-2 text-muted">Passport Number:</label>
                                    <div class="col-md-4">
                                        <span class="kt-font-bold">${driver.passport_number}</span>
                                    </div>
                                </div>

                                <div class="form-group row form-group-marginless kt-margin-t-20">
                                    <label class="col-md-2 text-muted">Passport Expiry:</label>
                                    <div class="col-md-4">
                                        <span class="kt-font-bold">${driver.passport_expiry??""}</span>
                                    </div>
                                    <label class="col-md-2 text-muted">Passport Picture:</label>
                                    <div class="col-md-4">
                                        <div class="kt-font-bold">
                                            ${driver.passport_pictures_front ?
                                            `
                                                <a target="_blank" href="${window.URL.createObjectURL(driver.passport_pictures_front)}" class="kt-media">
                                                    <img src="${window.URL.createObjectURL(driver.passport_pictures_front)}" />
                                                </a>
                                            `
                                            :
                                            `<span class="kt-font-danger">Not found</span>`
                                            }
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row form-group-marginless kt-margin-t-20">
                                    <label class="col-md-2 text-muted">Nationality:</label>
                                    <div class="col-md-4">
                                        <span class="kt-font-bold">${driver.nationality}</span>
                                    </div>
                                    <label class="col-md-2 text-muted">Additional Details:</label>
                                    <div class="col-md-4">
                                        <pre>${driver.additional_details}</pre>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="kt-separator kt-separator--space-md  kt-separator--border-solid"></div>

                        <!-- ------------------------ -->
                        <!--      VISA DETAILS       -->
                        <!-- ------------------------ -->
                        <div class="kt-section">

                            <div class="kt-section__content  kt-section__content--border">

                                <div class="kt-section__title d-flex justify-content-between">
                                    <span>Visa Details</span>
                                    <small class="kt-font-${driver.has_visa ? 'success' : 'danger'}">${driver.has_visa ? 'From: Driver' : 'From: Company'}</small>
                                </div>

                                ${driver.has_visa ? `

                                    ${driver.is_visa_skipped ? `
                                        <div class="alert alert-solid-danger alert-bold border-danger" role="alert">
                                            <div class="alert-text">This step was skipped. You must complete the missing fields/documents at a later time. During this period, the driver will remain on hold and cannot be allocated to any resources such as clients.</div>
                                        </div>
                                    ` : ''}

                                    <div class="form-group row form-group-marginless kt-margin-t-20">
                                        <label class="col-md-2 text-muted">Emirates ID:</label>
                                        <div class="col-md-4">
                                            <span class="kt-font-bold">${driver.emirates_id_no}</span>
                                        </div>
                                        <label class="col-md-2 text-muted">Emirates ID Pictures:</label>
                                        <div class="col-md-4">
                                            <div class="kt-font-bold">
                                                ${driver.emirates_id_pictures_front ?
                                                `
                                                    <a target="_blank" href="${window.URL.createObjectURL(driver.emirates_id_pictures_front)}" class="kt-media">
                                                        <img src="${window.URL.createObjectURL(driver.emirates_id_pictures_front)}" />
                                                    </a>
                                                `
                                                :
                                                `<span class="kt-font-danger"> Front: Not found </span>`
                                                }
                                                ${driver.emirates_id_pictures_back ?
                                                `
                                                    <a target="_blank" href="${window.URL.createObjectURL(driver.emirates_id_pictures_back)}" class="kt-media">
                                                        <img src="${window.URL.createObjectURL(driver.emirates_id_pictures_back)}" />
                                                    </a>
                                                `
                                                :
                                                `<span class="kt-font-danger"> Back: Not found </span>`
                                                }
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row form-group-marginless kt-margin-t-20">
                                        <label class="col-md-2 text-muted">Emirates ID Expiry:</label>
                                        <div class="col-md-4">
                                            <span class="kt-font-bold">${driver.emirates_id_expiry}</span>
                                        </div>
                                        <label class="col-md-2 text-muted">Visa Picture:</label>
                                        <div class="col-md-4">
                                            <div class="kt-font-bold">
                                                ${driver.visa_pictures_front ?
                                                `
                                                    <a target="_blank" href="${window.URL.createObjectURL(driver.visa_pictures_front)}" class="kt-media">
                                                        <img src="${window.URL.createObjectURL(driver.visa_pictures_front)}" />
                                                    </a>
                                                `
                                                :
                                                `<span class="kt-font-danger"> Not found </span>`
                                                }
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row form-group-marginless kt-margin-t-20">
                                        <label class="col-md-2 text-muted">Visa Expiry:</label>
                                        <div class="col-md-4">
                                            <span class="kt-font-bold">${driver.visa_expiry}</span>
                                        </div>
                                    </div>

                                ` : `
                                    <div class="kt-section__desc">
                                        ADDON: <span class="kt-font-brand">Visa</span>
                                    </div>

                                    <div class="form-group row form-group-marginless kt-margin-t-20">
                                        <label class="col-md-2 text-muted">Price:</label>
                                        <div class="col-md-4">
                                            <span class="kt-font-bold">${driver.addon['Visa'].price}</span>
                                        </div>
                                        <label class="col-md-2 text-muted">Override Default Settings:</label>
                                        <div class="col-md-4">
                                            <span class="kt-font-bold">${driver.addon['Visa'].override_setting ? 'YES' : 'NO'}</span>
                                        </div>
                                    </div>

                                    ${driver.addon['Visa'].override_setting ? `

                                        <table class="table table-sm table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Title</th>
                                                    <th>Display Title</th>
                                                    <th>Default Amount?</th>
                                                    <th>Charge from source?</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                ${driver.addon['Visa'].overrides.map(function(o, i){
                                                    return `

                                                        <tr>
                                                            <td>${i+1}</td>
                                                            <td>${o.title}</td>
                                                            <td>${o.display_title}</td>
                                                            <td>${o.amount}</td>
                                                            <td>${o.charge?'<i class="text-success flaticon2-check-mark"></i>':'<i class="text-danger flaticon2-cross"></i>'}</td>
                                                        </tr>

                                                    `;
                                                }).join('')}

                                            </tbody>
                                        </table>

                                    ` : ''}

                                `}

                            </div>
                        </div>

                        <div class="kt-separator kt-separator--space-md  kt-separator--border-solid"></div>

                        <!-- ------------------------ -->
                        <!--      LICENSE DETAILS       -->
                        <!-- ------------------------ -->
                        <div class="kt-section">

                            <div class="kt-section__content  kt-section__content--border">

                                <div class="kt-section__title d-flex justify-content-between">
                                    <span>License Details</span>
                                    <small class="kt-font-${driver.has_license ? 'success' : 'danger'}">${driver.has_license ? 'From: Driver' : 'From: Company'}</small>
                                </div>

                                ${driver.has_license ? `

                                    ${driver.is_license_skipped ? `
                                        <div class="alert alert-solid-danger alert-bold border-danger" role="alert">
                                            <div class="alert-text">This step was skipped. You must complete the missing fields/documents at a later time. During this period, the driver will remain on hold and cannot be allocated to any resources such as clients.</div>
                                        </div>
                                    ` : ''}

                                    <div class="form-group row form-group-marginless kt-margin-t-20">
                                        <label class="col-md-2 text-muted">License Number:</label>
                                        <div class="col-md-4">
                                            <span class="kt-font-bold">${driver.liscence_number}</span>
                                        </div>
                                        <label class="col-md-2 text-muted">License Pictures:</label>
                                        <div class="col-md-4">
                                            <div class="kt-font-bold">
                                                ${driver.liscence_pictures_front ?
                                                `
                                                    <a target="_blank" href="${window.URL.createObjectURL(driver.liscence_pictures_front)}" class="kt-media">
                                                        <img src="${window.URL.createObjectURL(driver.liscence_pictures_front)}" />
                                                    </a>
                                                `
                                                :
                                                `<span class="kt-font-danger"> Front: Not found </span>`
                                                }
                                                ${driver.liscence_pictures_back ?
                                                `
                                                    <a target="_blank" href="${window.URL.createObjectURL(driver.liscence_pictures_back)}" class="kt-media">
                                                        <img src="${window.URL.createObjectURL(driver.liscence_pictures_back)}" />
                                                    </a>
                                                `
                                                :
                                                `<span class="kt-font-danger"> Back: Not found </span>`
                                                }
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row form-group-marginless kt-margin-t-20">
                                        <label class="col-md-2 text-muted">License Expiry:</label>
                                        <div class="col-md-4">
                                            <span class="kt-font-bold">${driver.liscence_expiry}</span>
                                        </div>
                                    </div>

                                ` : `
                                    <div class="kt-section__desc">
                                        ADDON: <span class="kt-font-brand">${license_addon_name}</span>
                                    </div>

                                    <div class="form-group row form-group-marginless kt-margin-t-20">
                                        <label class="col-md-2 text-muted">Price:</label>
                                        <div class="col-md-4">
                                            <span class="kt-font-bold">${driver.addon[license_addon_name].price}</span>
                                        </div>
                                        <label class="col-md-2 text-muted">Override Default Settings:</label>
                                        <div class="col-md-4">
                                            <span class="kt-font-bold">${driver.addon[license_addon_name].override_setting ? 'YES' : 'NO'}</span>
                                        </div>
                                    </div>

                                    ${driver.addon[license_addon_name].override_setting ? `

                                        <table class="table table-sm table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Title</th>
                                                    <th>Display Title</th>
                                                    <th>Default Amount?</th>
                                                    <th>Charge from source?</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                ${driver.addon[license_addon_name].overrides.map(function(o, i){
                                                    return `

                                                        <tr>
                                                            <td>${i+1}</td>
                                                            <td>${o.title}</td>
                                                            <td>${o.display_title}</td>
                                                            <td>${o.amount}</td>
                                                            <td>${o.charge?'<i class="text-success flaticon2-check-mark"></i>':'<i class="text-danger flaticon2-cross"></i>'}</td>
                                                        </tr>

                                                    `;
                                                }).join('')}

                                            </tbody>
                                        </table>

                                    ` : ''}

                                `}

                            </div>
                        </div>

                        <div class="kt-separator kt-separator--space-md  kt-separator--border-solid"></div>

                        <!-- ------------------------ -->
                        <!--      RTA DETAILS       -->
                        <!-- ------------------------ -->
                        <div class="kt-section">

                            <div class="kt-section__content  kt-section__content--border">

                                <div class="kt-section__title d-flex justify-content-between">
                                    <span>RTA Details</span>
                                    <small class="kt-font-${driver.has_rta ? 'success' : 'danger'}">${driver.has_rta ? 'From: Driver' : 'From: Company'}</small>
                                </div>

                                ${driver.has_rta ? `

                                    ${driver.is_rta_skipped ? `
                                        <div class="alert alert-solid-danger alert-bold border-danger" role="alert">
                                            <div class="alert-text">This step was skipped. You must complete the missing fields/documents at a later time. During this period, the driver will remain on hold and cannot be allocated to any resources such as clients.</div>
                                        </div>
                                    ` : ''}

                                    <div class="form-group row form-group-marginless kt-margin-t-20">
                                        <label class="col-md-2 text-muted">RTA Permit Number:</label>
                                        <div class="col-md-4">
                                            <span class="kt-font-bold">${driver.rta_permit_number}</span>
                                        </div>
                                        <label class="col-md-2 text-muted">RTA Permit Pictures:</label>
                                        <div class="col-md-4">
                                            <div class="kt-font-bold">
                                                ${driver.rta_permit_pictures_front ?
                                                `
                                                    <a target="_blank" href="${window.URL.createObjectURL(driver.rta_permit_pictures_front)}" class="kt-media">
                                                        <img src="${window.URL.createObjectURL(driver.rta_permit_pictures_front)}" />
                                                    </a>
                                                `
                                                :
                                                `<span class="kt-font-danger"> Front: Not found </span>`
                                                }
                                                ${driver.rta_permit_pictures_back ?
                                                `
                                                    <a target="_blank" href="${window.URL.createObjectURL(driver.rta_permit_pictures_back)}" class="kt-media">
                                                        <img src="${window.URL.createObjectURL(driver.rta_permit_pictures_back)}" />
                                                    </a>
                                                `
                                                :
                                                `<span class="kt-font-danger"> Back: Not found </span>`
                                                }
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row form-group-marginless kt-margin-t-20">
                                        <label class="col-md-2 text-muted">RTA Permit Expiry:</label>
                                        <div class="col-md-4">
                                            <span class="kt-font-bold">${driver.rta_permit_expiry}</span>
                                        </div>
                                    </div>

                                ` : `
                                    <div class="kt-section__desc">
                                        ADDON: <span class="kt-font-brand">RTA</span>
                                    </div>

                                    <div class="form-group row form-group-marginless kt-margin-t-20">
                                        <label class="col-md-2 text-muted">Price:</label>
                                        <div class="col-md-4">
                                            <span class="kt-font-bold">${driver.addon['RTA'].price}</span>
                                        </div>
                                        <label class="col-md-2 text-muted">Override Default Settings:</label>
                                        <div class="col-md-4">
                                            <span class="kt-font-bold">${driver.addon['RTA'].override_setting ? 'YES' : 'NO'}</span>
                                        </div>
                                    </div>

                                    ${driver.addon['RTA'].override_setting ? `

                                        <table class="table table-sm table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Title</th>
                                                    <th>Display Title</th>
                                                    <th>Default Amount?</th>
                                                    <th>Charge from source?</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                ${driver.addon['RTA'].overrides.map(function(o, i){
                                                    return `

                                                        <tr>
                                                            <td>${i+1}</td>
                                                            <td>${o.title}</td>
                                                            <td>${o.display_title}</td>
                                                            <td>${o.amount}</td>
                                                            <td>${o.charge?'<i class="text-success flaticon2-check-mark"></i>':'<i class="text-danger flaticon2-cross"></i>'}</td>
                                                        </tr>

                                                    `;
                                                }).join('')}

                                            </tbody>
                                        </table>

                                    ` : ''}

                                `}

                            </div>
                        </div>
                    `;


                    // Append html
                    $(DRIVER_MODULE.container + ' .preview-data-container').html(html);
                }

                // Private functions
                var initWizard = function () {
                    // Initialize form wizard
                    wizard = new KTWizard(wizardId, {
                        startStep: 1,
                        manualStepForward: false
                    });

                    // Validation before going to next page
                    wizard.on('beforeNext', function(wizardObj) {
                        wizardObj.stop();  // don't go to the next step
                    });

                    // wizard.on('beforePrev', function(wizardObj) {
                    //     if (validator.form() !== true) {
                    //         wizardObj.stop();  // don't go to the next step
                    //     }
                    // });

                    // Change event
                    wizard.on('change', function(wizard) {
                        onChange();
                    });
                }

                var initValidation = function() {
                    validator = formEl.validate({
                        // Validate only visible fields
                        ignore: ":hidden",

                        // Validation rules
                        rules: {
                            //= Basic Information(step 1)
                            name: {
                                required: true
                            },
                            email: {
                                required: true,
                                email: true
                            }

                        },

                        // Display error
                        invalidHandler: function(event, validator) {
                            KTUtil.scrollTop();

                            swal.fire({
                                "title": "",
                                "text": "There are some errors in your submission. Please correct them.",
                                "type": "error",
                                "confirmButtonClass": "btn btn-primary m-btn m-btn--wide"
                            });
                        },

                        // Submit valid form
                        submitHandler: function (form) {

                            console.log("Submitted");

                        }
                    });
                }


                // ---------------------------
                //  Move to [next/prev] steps
                // ---------------------------
                var ChangeStep = function(action = "next"){
                    // action = next/prev

                    if(action === "next") wizard.goNext(false);
                    if(action === "prev") wizard.goPrev(false);
                    if(action === "submit"){
                        // Form was submitted, show alert

                        swal.fire({
                            "title": "Saved!!",
                            "text": "The driver has been created submitted!",
                            "type": "success",
                            "confirmButtonClass": "btn btn-primary"
                        });

                        if(!driverId){
                            window.location.href = "{{ route('tenant.admin.drivers.driver.view') }}";
                        }
                        else{
                            window.location.href = "{{ route('tenant.admin.drivers.edit', '_:param') }}".replace('_:param', driverId);

                        }


                        return;
                    }

                    onChange();
                }


                // --------------------------------------
                //  Save Data: according to current step
                // --------------------------------------
                var SaveData = function(action = "next"){

                    return new Promise(function(resolve, reject) {

                        // For now, save data only on "submit" action
                        if(action === "prev" || action === "next") {
                            return resolve(null);
                        }

                        var formData = new FormData(formEl[0]);

                        // Append step info to formData, prefixed with "wz"
                        formData.append("wz_step", wizard.getStep());
                        formData.append("wz_action", action);

                        $.ajax({
                            url : "{{route('tenant.admin.drivers.add')}}",
                            type : 'POST',
                            headers: {
                                'X-NOFETCH': ''
                            },
                            cache: false,
                            contentType: false,
                            processData: false,
                            data: formData
                        })
                        .done(function (response) {
                            resolve(response);
                        })
                        .fail(function(jqXHR, textStatus, errorThrown) {
                            reject(jqXHR, textStatus, errorThrown);
                        });

                    });

                }

                var initSubmit = function() {
                    // Event on Submit form


                    var btn = formEl.find('[data-custom-ktwizard-type="action-submit"], [data-custom-ktwizard-type="action-next"], [data-custom-ktwizard-type="action-prev"]');
                    btn.off('click').on('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        e.stopImmediatePropagation();

                        var actionType = this.getAttribute('data-custom-ktwizard-type').replace(/action-/i, '').toLowerCase();

                        if(actionType === "prev"){

                            // GOTO Next/Prev Step
                            ChangeStep(actionType);

                        }
                        else{


                            if (validator.form()) {
                                KTApp.progress(btn);
                                // KTApp.block(formEl);

                                SaveData(actionType)
                                .then(function(response){

                                    if(!!response && !!response.driver_id) driverId = response.driver_id;

                                    // GOTO Next/Prev Step
                                    ChangeStep(actionType);

                                })
                                .catch(function(jqXHR, textStatus, errorThrown){

                                    /* this will handle & show errors */
                                    kingriders.Plugins.KR_AJAX.showErrors(jqXHR);

                                })
                                .finally(() => {
                                    KTApp.unprogress(btn);
                                    // KTApp.unblock(formEl);
                                });
                            }
                        }
                    });
                }

                return {

                    // public functions
                    init: function() {
                        wizardEl = KTUtil.get(wizardId);
                        formEl = $(DRIVER_MODULE.container+' form');

                        initWizard();
                        initValidation();
                        initSubmit();

                        console.log("validator", validator);
                        console.log("wizard", wizard);
                    },

                    getVars: function(){
                        return {wizardEl, wizard, formEl, validator, continueAnyway};
                    },

                    changeStep: ChangeStep,
                    triggerChange: onChange,

                    getStepTitle: function(){
                        var stepTitles = {
                            1: 'basic',
                            2: 'visa',
                            3: 'license',
                            4: 'rta',
                            5: 'preview',
                        };

                        var currentStep = wizard.getStep();

                        return stepTitles[currentStep];
                    }
                };
            }()

        };

        $(function() {


            DRIVER_MODULE.wizard.init();
            DRIVER_MODULE.phone.init();
            DRIVER_MODULE.addon_module.init();

            // ------------------
            // Listeners
            // ------------------
            $(DRIVER_MODULE.container + " [type=checkbox][data-addonselection]").on('change', function(){
                var target_type = $(this).data('addonselection');
                var checked = this.checked;

                // Show when checked
                $(DRIVER_MODULE.container + ' [data-addonselection="'+target_type+'"][data-addonselection-when="1"]').toggle(checked);

                // Hide when checked
                $(DRIVER_MODULE.container + ' [data-addonselection="'+target_type+'"][data-addonselection-when="0"]').toggle(!checked);


                DRIVER_MODULE.wizard.triggerChange();
            });

            $(DRIVER_MODULE.container + " [type=radio][data-addonselection]").on('change', function(){
                var target_type = $(this).data('addonselection');
                var name = $(this).attr('name');
                var checkedValue = $(DRIVER_MODULE.container + " [type=radio][data-addonselection][name="+name+"]:checked").val();

                // Hide all
                $(DRIVER_MODULE.container + ' [data-addonselection="'+target_type+'"]').hide();

                // Show found el
                $(DRIVER_MODULE.container + ' [data-addonselection="'+target_type+'"][data-addonselection-when="'+checkedValue+'"]').show();
            });

            $(DRIVER_MODULE.container + ' [data-custom-ktwizard-type="action-continue"] a').on('click', function(e){
                e.preventDefault();

                var vars = DRIVER_MODULE.wizard.getVars();
                var currentStep = vars.wizard.getStep();
                if(vars.continueAnyway){

                    swal.fire({
                        title: 'Are you sure?',
                        text: "You must complete the missing fields/documents at a later time. During this period, the driver will remain on hold and cannot be allocated to any resources such as clients.",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, continue!',
                        scrollbarPadding: false,
                        allowOutsideClick: function() {
                            return !swal.isLoading()
                        }
                    })
                    .then(function(result) {
                        if (result.value) {

                            // Append hidden input, indicating this step was skipped
                            var stepTitle = DRIVER_MODULE.wizard.getStepTitle();
                            var name = 'is_'+stepTitle+'_skipped';
                            $(DRIVER_MODULE.container + ' form [name="'+name+'"]').remove();
                            $(DRIVER_MODULE.container + ' form').prepend('<input type="hidden" name="'+name+'" class="skipped-step-input" value="1" />')


                            // Move to next step
                            DRIVER_MODULE.wizard.changeStep("next");

                        }
                    });

                }



            });


            // -------------------
            // Execution
            // -------------------
            $(DRIVER_MODULE.container + " [type=checkbox][data-addonselection]").trigger('change');
            $(DRIVER_MODULE.container + " [type=radio][data-addonselection]").trigger('change');
        });
    </script>

@endsection
