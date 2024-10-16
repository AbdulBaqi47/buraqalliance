@extends('Tenant.layouts.app')

@section('page_title')
    Create Driver
@endsection
@section('head')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/css/intlTelInput.css">
    <style>
        .iti {
            width: 100%;
        }

        /* Adjust the width of the input field to be 100% of the ITI container */
        .iti__input {
            width: 100%;
        }
    </style>
@endsection
@section('content')
    <!--begin::Portlet-->
    <div class="kt-portlet mt-5" id="kt-portlet__create-driver" kr-ajax-content>
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">Create Driver</h3>
            </div>
            <div class="kt-portlet__head-label" kr-ajax-closebtn></div>
        </div>
        <!--begin::Form-->
        <form class="kt-form" enctype="multipart/form-data" action="{{ route('tenant.admin.drivers.add') }}" method="POST">
            @csrf
            <div class="kt-portlet__body">
                {{-- Name --}}
                <div class="form-group">
                    <label>Name <span class="text-danger">*<span></label>
                    <input type="text" autocomplete="off" name="name" required
                        class="form-control @error('name') is-invalid @enderror" placeholder="Enter Name"
                        value="{{ old('name') }}">
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
                    <input type="email" autocomplete="off" name="email" required
                        class="form-control @error('email') is-invalid @enderror" placeholder="Enter Email"
                        value="{{ old('email') }}">
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
                    <input type="text" required readonly name="date_of_birth" data-state="date"
                        class="kr-datepicker form-control @error('date_of_birth') is-invalid @enderror"
                        data-default="{{ old('date_of_birth') }}">
                    @if ($errors->has('date_of_birth'))
                        <span class="invalid-response text-danger" role="alert">
                            <strong>
                                {{ $errors->first('date_of_birth') }}
                            </strong>
                        </span>
                    @endif

                </div>
                {{-- Hidden Field To Identify Booking / Vehicle --}}
                <input type="text" name="status" id="status" style="display: none">
                {{-- Assign Booking / Vehicle --}}
                <div class="form-group" id="assignBooking">
                    <label>Booking / Vehicle <span class="text-danger">*</span></label>
                    <button onclick="ASSIGN_BOOKING_MODULE.handleShowAllBtnClick(event)" class=" btn btn-warning text-white btn-sm p-1 m-1 float-right">Show All</button>
                    <select required onchange="handleBookingIdChange(this)" id="booking_id" class="form-control kr-select2" data-source="ASSIGN_BOOKING_MODULE.bookings()" name="booking_id">
                        <option value=""></option>
                    </select>
                    @if ($errors->has('booking_id'))
                        <span class="invalid-response text-danger" role="alert">
                            <strong>
                                {{ $errors->first('booking_id') }}
                            </strong>
                        </span>
                    @endif

                </div>
                {{-- Phone Number --}}
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" autocomplete="off" name="phone_number" class="form-control @error('full_phone') is-invalid @enderror" placeholder="Enter Number" value="{{ old('full_phone') }}">
                    <span id="valid-msg" class="hide">i.e. 055 123 4567</span>
                    </br>
                    <span id="error-msg" class="hide text-danger"></span>
                    @if ($errors->has('full_phone'))
                    </br>
                    <span class="invalid-response text-danger" role="alert">
                            <strong>
                                {{ str_replace('full_phone','Phone Number',$errors->first('full_phone')) }}
                            </strong>
                        </span>
                    @endif

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
                {{-- Liscence Number --}}
                <div class="form-group">
                    <label>Liscence Number </label>
                    <input type="text" autocomplete="off" name="liscence_number"
                        class="form-control @error('liscence_number') is-invalid @enderror" placeholder="Enter Liscence"
                        value="{{ old('liscence_number') }}">
                    @if ($errors->has('liscence_number'))
                        <span class="invalid-response text-danger" role="alert">
                            <strong>
                                {{ $errors->first('liscence_number') }}
                            </strong>
                        </span>
                    @endif
                </div>
                {{-- Liscence Images --}}
                <div class="form-group">
                    <label>Uplaod Liscence Pictures </label>
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="kt-uppy kr-uppy" uppy-size="20" uppy-max="1" uppy-min="1"
                                uppy-label="Front Picture" uppy-input="liscence_pictures_front"></div>
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
                            <div class="kt-uppy kr-uppy" uppy-size="20" uppy-max="1" uppy-min="1"
                                uppy-label="Back Picture" uppy-input="liscence_pictures_back"></div>
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
                {{-- Liscence Expiry --}}
                <div class="form-group">
                    <label>Liscence Expiry </label>
                    <input type="text" required readonly name="liscence_expiry" data-state="date"
                        class="kr-datepicker form-control @error('liscence_expiry') is-invalid @enderror"
                        data-default="{{ old('liscence_expiry') }}">
                    @if ($errors->has('liscence_expiry'))
                        <span class="invalid-response text-danger" role="alert">
                            <strong>
                                {{ $errors->first('liscence_expiry') }}
                            </strong>
                        </span>
                    @endif

                </div>
                {{-- Emirates ID --}}
                <div class="form-group">
                    <label>Emirates ID </label>
                    <input type="text" autocomplete="off" name="emirates_id_no"
                        class="form-control @error('emirates_id_no') is-invalid @enderror" placeholder="Emirates ID"
                        value="{{ old('emirates_id_no') }}">
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
                    <label>Uplaod Emirates ID Pictures </label>
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="kt-uppy kr-uppy" uppy-size="20" uppy-max="1" uppy-min="1"
                                uppy-label="Front Picture" uppy-input="emirates_id_pictures_front"></div>
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
                            <div class="kt-uppy kr-uppy" uppy-size="20" uppy-max="1" uppy-min="1"
                                uppy-label="Back Picture" uppy-input="emirates_id_pictures_back"></div>
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
                    <label>Emirates ID Expiry </label>
                    <input type="text" required readonly name="emirates_id_expiry" data-state="date"
                        class="kr-datepicker form-control @error('emirates_id_expiry') is-invalid @enderror"
                        data-default="{{ old('emirates_id_expiry') }}">
                    @if ($errors->has('emirates_id_expiry'))
                        <span class="invalid-response text-danger" role="alert">
                            <strong>
                                {{ $errors->first('emirates_id_expiry') }}
                            </strong>
                        </span>
                    @endif

                </div>
                {{-- Passport Number --}}
                <div class="form-group">
                    <label>Passport Number <span class="text-danger">*<span></label>
                    <input type="text" autocomplete="off" name="passport_number" required class="form-control @error('passport_number') is-invalid @enderror" placeholder="Enter Passport" value="{{ old('passport_number') }}">
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
                            <div class="kt-uppy kr-uppy" uppy-size="20" uppy-max="1" uppy-min="1"
                                uppy-label="Passport Picture" uppy-input="passport_pictures_front"></div>
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
                    <input type="text" required readonly name="passport_expiry" data-state="date"
                        class="kr-datepicker form-control @error('passport_expiry') is-invalid @enderror"
                        data-default="{{ old('passport_expiry') }}">
                    @if ($errors->has('passport_expiry'))
                        <span class="invalid-response text-danger" role="alert">
                            <strong>
                                {{ $errors->first('passport_expiry') }}
                            </strong>
                        </span>
                    @endif

                </div>
                {{-- Visa Images --}}
                <div class="form-group">
                    <label>Visa Picture </label>
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="kt-uppy kr-uppy" uppy-size="20" uppy-max="1" uppy-min="1"
                                uppy-label="VISA Picture" uppy-input="visa_pictures_front"></div>
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
                    <label>Visa Expiry </label>
                    <input type="text" required readonly name="visa_expiry" data-state="date"
                        class="kr-datepicker form-control @error('visa_expiry') is-invalid @enderror"
                        data-default="{{ old('visa_expiry') }}">
                    @if ($errors->has('visa_expiry'))
                        <span class="invalid-response text-danger" role="alert">
                            <strong>
                                {{ $errors->first('visa_expiry') }}
                            </strong>
                        </span>
                    @endif

                </div>
                {{-- RTA PERMIT Number --}}
                <div class="form-group">
                    <label>RTA Permit Number </label>
                    <input type="text" autocomplete="off" name="rta_permit_number"
                        class="form-control @error('passport_number') is-invalid @enderror" placeholder="RTA Permit Number"
                        value="{{ old('rta_permit_number') }}">
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
                    <label>RTA Permit Expiry </label>
                    <input type="text" required readonly name="rta_permit_expiry" data-state="date"
                        class="kr-datepicker form-control @error('rta_permit_expiry') is-invalid @enderror"
                        data-default="{{ old('rta_permit_expiry') }}">
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
                    <label>RTA Permit Pictures </label>
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="kt-uppy kr-uppy" uppy-size="20" uppy-max="1" uppy-min="1"
                                uppy-label="Front Picture" uppy-input="rta_permit_pictures_front"></div>
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
                            <div class="kt-uppy kr-uppy" uppy-size="20" uppy-max="1" uppy-min="1"
                                uppy-label="Back Picture" uppy-input="rta_permit_pictures_back"></div>
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
                {{-- Nationality --}}
                <div class="form-group">
                    <label>Nationality </label>
                    <select class="form-control kr-select2 @if ($errors->has('nationality')) invalid-field @endif"
                        name="nationality">
                        <option value=""></option>
                        <option @if (old('nationality') === 'andorran') selected @endif value="andorran">Andorran</option>
                        <option @if (old('nationality') === 'angolan') selected @endif value="angolan">Angolan</option>
                        <option @if (old('nationality') === 'antiguans') selected @endif value="antiguans">Antiguans</option>
                        <option @if (old('nationality') === 'argentinean') selected @endif value="argentinean">Argentinean
                        </option>
                        <option @if (old('nationality') === 'afghan') selected @endif value="afghan">Afghan</option>
                        <option @if (old('nationality') === 'albanian') selected @endif value="albanian">Albanian</option>
                        <option @if (old('nationality') === 'algerian') selected @endif value="algerian">Algerian</option>
                        <option @if (old('nationality') === 'american') selected @endif value="american">American</option>
                        <option @if (old('nationality') === 'armenian') selected @endif value="armenian">Armenian</option>
                        <option @if (old('nationality') === 'australian') selected @endif value="australian">Australian</option>
                        <option @if (old('nationality') === 'austrian') selected @endif value="austrian">Austrian</option>
                        <option @if (old('nationality') === 'azerbaijani') selected @endif value="azerbaijani">Azerbaijani
                        </option>
                        <option @if (old('nationality') === 'bahamian') selected @endif value="bahamian">Bahamian</option>
                        <option @if (old('nationality') === 'bahraini') selected @endif value="bahraini">Bahraini</option>
                        <option @if (old('nationality') === 'bangladeshi') selected @endif value="bangladeshi">Bangladeshi
                        </option>
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
                        <option @if (old('nationality') === 'cameroonian') selected @endif value="cameroonian">Cameroonian
                        </option>
                        <option @if (old('nationality') === 'canadian') selected @endif value="canadian">Canadian</option>
                        <option @if (old('nationality') === 'cape') selected @endif value="cape verdean">Cape Verdean
                        </option>
                        <option @if (old('nationality') === 'central') selected @endif value="central african">Central African
                        </option>
                        <option @if (old('nationality') === 'chadian') selected @endif value="chadian">Chadian</option>
                        <option @if (old('nationality') === 'chilean') selected @endif value="chilean">Chilean</option>
                        <option @if (old('nationality') === 'chinese') selected @endif value="chinese">Chinese</option>
                        <option @if (old('nationality') === 'colombian') selected @endif value="colombian">Colombian</option>
                        <option @if (old('nationality') === 'comoran') selected @endif value="comoran">Comoran</option>
                        <option @if (old('nationality') === 'congolese') selected @endif value="congolese">Congolese</option>
                        <option @if (old('nationality') === 'costa') selected @endif value="costa rican">Costa Rican
                        </option>
                        <option @if (old('nationality') === 'croatian') selected @endif value="croatian">Croatian</option>
                        <option @if (old('nationality') === 'cuban') selected @endif value="cuban">Cuban</option>
                        <option @if (old('nationality') === 'cypriot') selected @endif value="cypriot">Cypriot</option>
                        <option @if (old('nationality') === 'czech') selected @endif value="czech">Czech</option>
                        <option @if (old('nationality') === 'danish') selected @endif value="danish">Danish</option>
                        <option @if (old('nationality') === 'djibouti') selected @endif value="djibouti">Djibouti</option>
                        <option @if (old('nationality') === 'dominican') selected @endif value="dominican">Dominican</option>
                        <option @if (old('nationality') === 'dutch') selected @endif value="dutch">Dutch</option>
                        <option @if (old('nationality') === 'east') selected @endif value="east timorese">East Timorese
                        </option>
                        <option @if (old('nationality') === 'ecuadorean') selected @endif value="ecuadorean">Ecuadorean</option>
                        <option @if (old('nationality') === 'egyptian') selected @endif value="egyptian">Egyptian</option>
                        <option @if (old('nationality') === 'emirian') selected @endif value="emirian">Emirian</option>
                        <option @if (old('nationality') === 'equatorial') selected @endif value="equatorial guinean">Equatorial
                            Guinean</option>
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
                        <option @if (old('nationality') === 'guinea') selected @endif value="guinea-bissauan">Guinea-Bissauan
                        </option>
                        <option @if (old('nationality') === 'guinean') selected @endif value="guinean">Guinean</option>
                        <option @if (old('nationality') === 'guyanese') selected @endif value="guyanese">Guyanese</option>
                        <option @if (old('nationality') === 'haitian') selected @endif value="haitian">Haitian</option>
                        <option @if (old('nationality') === 'herzegovinian') selected @endif value="herzegovinian">Herzegovinian
                        </option>
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
                        <option @if (old('nationality') === 'kazakhstani') selected @endif value="kazakhstani">Kazakhstani
                        </option>
                        <option @if (old('nationality') === 'kenyan') selected @endif value="kenyan">Kenyan</option>
                        <option @if (old('nationality') === 'kittian') selected @endif value="kittian and nevisian">Kittian
                            and Nevisian</option>
                        <option @if (old('nationality') === 'kuwaiti') selected @endif value="kuwaiti">Kuwaiti</option>
                        <option @if (old('nationality') === 'kyrgyz') selected @endif value="kyrgyz">Kyrgyz</option>
                        <option @if (old('nationality') === 'laotian') selected @endif value="laotian">Laotian</option>
                        <option @if (old('nationality') === 'latvian') selected @endif value="latvian">Latvian</option>
                        <option @if (old('nationality') === 'lebanese') selected @endif value="lebanese">Lebanese</option>
                        <option @if (old('nationality') === 'liberian') selected @endif value="liberian">Liberian</option>
                        <option @if (old('nationality') === 'libyan') selected @endif value="libyan">Libyan</option>
                        <option @if (old('nationality') === 'liechtensteiner') selected @endif value="liechtensteiner">
                            Liechtensteiner</option>
                        <option @if (old('nationality') === 'lithuanian') selected @endif value="lithuanian">Lithuanian
                        </option>
                        <option @if (old('nationality') === 'luxembourger') selected @endif value="luxembourger">Luxembourger
                        </option>
                        <option @if (old('nationality') === 'macedonian') selected @endif value="macedonian">Macedonian
                        </option>
                        <option @if (old('nationality') === 'malagasy') selected @endif value="malagasy">Malagasy</option>
                        <option @if (old('nationality') === 'malawian') selected @endif value="malawian">Malawian</option>
                        <option @if (old('nationality') === 'malaysian') selected @endif value="malaysian">Malaysian</option>
                        <option @if (old('nationality') === 'maldivan') selected @endif value="maldivan">Maldivan</option>
                        <option @if (old('nationality') === 'malian') selected @endif value="malian">Malian</option>
                        <option @if (old('nationality') === 'maltese') selected @endif value="maltese">Maltese</option>
                        <option @if (old('nationality') === 'marshallese') selected @endif value="marshallese">Marshallese
                        </option>
                        <option @if (old('nationality') === 'mauritanian') selected @endif value="mauritanian">Mauritanian
                        </option>
                        <option @if (old('nationality') === 'mauritian') selected @endif value="mauritian">Mauritian</option>
                        <option @if (old('nationality') === 'mexican') selected @endif value="mexican">Mexican</option>
                        <option @if (old('nationality') === 'micronesian') selected @endif value="micronesian">Micronesian
                        </option>
                        <option @if (old('nationality') === 'moldovan') selected @endif value="moldovan">Moldovan</option>
                        <option @if (old('nationality') === 'monacan') selected @endif value="monacan">Monacan</option>
                        <option @if (old('nationality') === 'mongolian') selected @endif value="mongolian">Mongolian</option>
                        <option @if (old('nationality') === 'moroccan') selected @endif value="moroccan">Moroccan</option>
                        <option @if (old('nationality') === 'mosotho') selected @endif value="mosotho">Mosotho</option>
                        <option @if (old('nationality') === 'motswana') selected @endif value="motswana">Motswana</option>
                        <option @if (old('nationality') === 'mozambican') selected @endif value="mozambican">Mozambican
                        </option>
                        <option @if (old('nationality') === 'namibian') selected @endif value="namibian">Namibian</option>
                        <option @if (old('nationality') === 'nauruan') selected @endif value="nauruan">Nauruan</option>
                        <option @if (old('nationality') === 'nepalese') selected @endif value="nepalese">Nepalese</option>
                        <option @if (old('nationality') === 'new') selected @endif value="new zealander">New Zealander
                        </option>
                        <option @if (old('nationality') === 'ni') selected @endif value="ni-vanuatu">Ni-Vanuatu
                        </option>
                        <option @if (old('nationality') === 'nicaraguan') selected @endif value="nicaraguan">Nicaraguan
                        </option>
                        <option @if (old('nationality') === 'nigerien') selected @endif value="nigerien">Nigerien</option>
                        <option @if (old('nationality') === 'north') selected @endif value="north korean">North Korean
                        </option>
                        <option @if (old('nationality') === 'northern') selected @endif value="northern irish">Northern Irish
                        </option>
                        <option @if (old('nationality') === 'norwegian') selected @endif value="norwegian">Norwegian</option>
                        <option @if (old('nationality') === 'omani') selected @endif value="omani">Omani</option>
                        <option @if (old('nationality') === 'pakistani') selected @endif value="pakistani">Pakistani</option>
                        <option @if (old('nationality') === 'palauan') selected @endif value="palauan">Palauan</option>
                        <option @if (old('nationality') === 'panamanian') selected @endif value="panamanian">Panamanian
                        </option>
                        <option @if (old('nationality') === 'papua') selected @endif value="papua new guinean">Papua New
                            Guinean</option>
                        <option @if (old('nationality') === 'paraguayan') selected @endif value="paraguayan">Paraguayan
                        </option>
                        <option @if (old('nationality') === 'peruvian') selected @endif value="peruvian">Peruvian</option>
                        <option @if (old('nationality') === 'polish') selected @endif value="polish">Polish</option>
                        <option @if (old('nationality') === 'portuguese') selected @endif value="portuguese">Portuguese
                        </option>
                        <option @if (old('nationality') === 'qatari') selected @endif value="qatari">Qatari</option>
                        <option @if (old('nationality') === 'romanian') selected @endif value="romanian">Romanian</option>
                        <option @if (old('nationality') === 'russian') selected @endif value="russian">Russian</option>
                        <option @if (old('nationality') === 'rwandan') selected @endif value="rwandan">Rwandan</option>
                        <option @if (old('nationality') === 'saint') selected @endif value="saint lucian">Saint Lucian
                        </option>
                        <option @if (old('nationality') === 'salvadoran') selected @endif value="salvadoran">Salvadoran
                        </option>
                        <option @if (old('nationality') === 'samoan') selected @endif value="samoan">Samoan</option>
                        <option @if (old('nationality') === 'san') selected @endif value="san marinese">San Marinese
                        </option>
                        <option @if (old('nationality') === 'sao') selected @endif value="sao tomean">Sao Tomean
                        </option>
                        <option @if (old('nationality') === 'saudi') selected @endif value="saudi">Saudi</option>
                        <option @if (old('nationality') === 'scottish') selected @endif value="scottish">Scottish</option>
                        <option @if (old('nationality') === 'senegalese') selected @endif value="senegalese">Senegalese
                        </option>
                        <option @if (old('nationality') === 'serbian') selected @endif value="serbian">Serbian</option>
                        <option @if (old('nationality') === 'seychellois') selected @endif value="seychellois">Seychellois
                        </option>
                        <option @if (old('nationality') === 'sierra') selected @endif value="sierra leonean">Sierra Leonean
                        </option>
                        <option @if (old('nationality') === 'singaporean') selected @endif value="singaporean">Singaporean
                        </option>
                        <option @if (old('nationality') === 'slovakian') selected @endif value="slovakian">Slovakian</option>
                        <option @if (old('nationality') === 'slovenian') selected @endif value="slovenian">Slovenian</option>
                        <option @if (old('nationality') === 'solomon') selected @endif value="solomon islander">Solomon
                            Islander</option>
                        <option @if (old('nationality') === 'somali') selected @endif value="somali">Somali</option>
                        <option @if (old('nationality') === 'south') selected @endif value="south african">South African
                        </option>
                        <option @if (old('nationality') === 'south') selected @endif value="south korean">South Korean
                        </option>
                        <option @if (old('nationality') === 'spanish') selected @endif value="spanish">Spanish</option>
                        <option @if (old('nationality') === 'sri') selected @endif value="sri lankan">Sri Lankan
                        </option>
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
                        <option @if (old('nationality') === 'trinidadian') selected @endif value="trinidadian or tobagonian">
                            Trinidadian or Tobagonian</option>
                        <option @if (old('nationality') === 'tunisian') selected @endif value="tunisian">Tunisian</option>
                        <option @if (old('nationality') === 'turkish') selected @endif value="turkish">Turkish</option>
                        <option @if (old('nationality') === 'tuvaluan') selected @endif value="tuvaluan">Tuvaluan</option>
                        <option @if (old('nationality') === 'ugandan') selected @endif value="ugandan">Ugandan</option>
                        <option @if (old('nationality') === 'ukrainian') selected @endif value="ukrainian">Ukrainian</option>
                        <option @if (old('nationality') === 'uruguayan') selected @endif value="uruguayan">Uruguayan</option>
                        <option @if (old('nationality') === 'uzbekistani') selected @endif value="uzbekistani">Uzbekistani
                        </option>
                        <option @if (old('nationality') === 'venezuelan') selected @endif value="venezuelan">Venezuelan
                        </option>
                        <option @if (old('nationality') === 'vietnamese') selected @endif value="vietnamese">Vietnamese
                        </option>
                        <option @if (old('nationality') === 'welsh') selected @endif value="welsh">Welsh</option>
                        <option @if (old('nationality') === 'yemenite') selected @endif value="yemenite">Yemenite</option>
                        <option @if (old('nationality') === 'zambian') selected @endif value="zambian">Zambian</option>
                        <option @if (old('nationality') === 'zimbabwean') selected @endif value="zimbabwean">Zimbabwean
                        </option>
                    </select>
                    @if ($errors->has('nationality'))
                        <span class="invalid-response text-danger" role="alert">
                            <strong>
                                {{ $errors->first('nationality') }}
                            </strong>
                        </span>
                    @endif
                </div>
                {{-- Passport Collection --}}
                <div class="form-group">
                    <label>Passport Collected </label>
                    <div class="col-3">
                        <span class="kt-switch">
                            <label>
                                <input type="checkbox" name="is_pasport_collected">
                                <span></span>
                            </label>
                        </span>
                    </div>
                </div>
                {{-- Additional Information --}}
                <div class="form-group form-group-last">
                    <label for="additional_details">Additional Details</label>
                    <textarea class="form-control" id="additional_details" name="additional_details" rows="3"></textarea>
                </div>

            </div>
            <div class="kt-portlet__foot kt-portlet__foot--solid">
                <div class="kt-form__actions kt-form__actions--right">
                    <button id="submitbtn" type="submit" class="btn btn-brand">Save</button>
                </div>
            </div>
        </form>
        <!--end::Form-->
    </div>

    <!--end::Portlet-->
@endsection

@section('foot')
    {{-- ----------------------------------------------------------------------------
                                SCRIPTS (use in current page)
    ------------------------------------------------------------------------------ --}}
    <script kr-ajax-head type="text/javascript">
        const handleBookingIdChange = (e) => {
            let selectedOption = e.options[e.selectedIndex].text[0];
            let statusEl = $('#status');
            selectedOption === 'B' ? statusEl.val('booking') : statusEl.val('vehicle');
        };
    </script>
    <script kr-ajax-head type="text/javascript">
        var ASSIGN_BOOKING_MODULE = {
            show_all: false,
            all_bookings: {!! $bookings !!},
            extractDriverNames(string) {
                // Find the "Drivers:" string.
                const driversStartIndex = string.indexOf("Drivers:");

                // If the "Drivers:" string is not found, return an empty array.
                if (driversStartIndex === -1) {
                    return [];
                }

                // Find the end of the driver names section.
                const driversEndIndex = string.indexOf(")", driversStartIndex + 8);

                // Get the driver names as a string.
                const driverNamesString = string.substring(driversStartIndex + 8, driversEndIndex);

                // Split the driver names string into an array of strings.
                const driverNames = driverNamesString.split(" | ");

                // Return the array of driver names.
                return driverNames;
            },
            handleShowAllBtnClick(e){
                e.preventDefault();
                let element = $(e.target);
                if(element.html() === 'Show All'){
                    ASSIGN_BOOKING_MODULE.show_all = true;
                    element.html('Show Less');
                }else{
                    ASSIGN_BOOKING_MODULE.show_all = false;
                    element.html('Show All');
                }
                kingriders.Plugins.update_select2(document.querySelector('#booking_id'));
            },
            bookings: function() {
                if(ASSIGN_BOOKING_MODULE.show_all){
                    return ASSIGN_BOOKING_MODULE.all_bookings;
                }
                return ASSIGN_BOOKING_MODULE.all_bookings.filter(item => {
                    let driverNames = ASSIGN_BOOKING_MODULE.extractDriverNames(item.text)
                    return driverNames.length < 2;
                });
            },
        };
    </script>
    <script kr-ajax-head type="text/javascript">
        var DRIVER_MODULE = {
            container: $('#kt-portlet__create-driver'),
            Utils: {

                reset_page: function() {
                    $('#kt-portlet__create-driver form [name=driver_id]').remove();
                    /* clear the items */
                    $('#kt-portlet__create-driver [name="client_id"]').val(null).trigger('change.select2');
                    $('#kt-portlet__create-driver [name="plate"]').val(null);
                    $('#kt-portlet__create-driver [name="model"]').val(null).trigger('change.select2');
                    $('#kt-portlet__create-driver [name="manufacturer"]').val(null).trigger('change.select2');
                    $('#kt-portlet__create-driver [name="cc"]').val(null);
                },
                load_page: function(driver) {

                    /* Load the job in page (this funtion is using in view job page) */

                    /* Update url */
                    var MODAL = $('#kt-portlet__create-driver').parents('.modal');
                    if (MODAL.length) {
                        kingriders.Plugins.KR_AJAX.ModalOnAir.update({
                            modal: MODAL,
                            url: "{{ url('admin/drivers') }}/" + driver.id + "/edit",
                            title: 'Edit driver | Administrator'
                        });
                    }

                    /* need to check if job is suitable for edit, (not in creating process) */
                    if (driver.actions.status == 1) {
                        /* check if page if loaded in modal */
                        var MODAL = $('#kt-portlet__create-driver').parents('.modal');
                        if (MODAL.length) {
                            MODAL.modal('show');
                        }

                        /* change the action of form to edit */
                        $('#kt-portlet__create-driver form [name=driver_id]').remove();
                        $('#kt-portlet__create-driver form').attr('action', $('#kt-portlet__create-driver form')
                                .attr(
                                    'data-edit'))
                            .prepend('<input type="hidden" name="driver_id" value="' + driver.id + '" />');


                        /* load other data like driver,client */
                        $('#kt-portlet__create-driver [name="client_id"]').val(driver.client.id).trigger(
                            'change.select2');
                        $('#kt-portlet__create-driver [name="plate"]').val(driver.plate);
                        $('#kt-portlet__create-driver [name="model"]').val(driver.model).trigger('change.select2');
                        $('#kt-portlet__create-driver [name="cc"]').val(driver.cc);

                        /* need to check if manufacture is not in the list */
                        if ($('#kt-portlet__create-driver [name="manufacturer"]').find('option[value="' + driver
                                .manufacturer + '"]').length == 0) {
                            /* need to add a new option in the list */
                            var newOption = new Option(driver.manufacturer, driver.manufacturer, false, true);
                            $('#kt-portlet__create-driver [name="manufacturer"]').append(newOption);
                            newOption.setAttribute('data-select2-tag', true);
                        }
                        $('#kt-portlet__create-driver [name="manufacturer"]').val(driver.manufacturer).trigger(
                            'change.select2');

                    } else {
                        /* cannot laod the job now */
                        swal.fire({
                            position: 'center',
                            type: 'error',
                            title: 'Cannot load driver',
                            html: 'driver is processing.. Please retry after some time',
                        });
                    }
                },
            },
            select2Utils: {
                formatWalking: function(option) {
                    if (!option.id) {
                        return option.text;
                    }
                    var id = option.id;
                    var client = DRIVER_MODULE.clients.find(function(x) {
                        return x.id == id
                    });
                    if (typeof client !== "undefined" && client) {
                        if (typeof client.walking_customer !== "undefined" && client.walking_customer == 1) {
                            return $('<span>' + option.text + '<i class="fa fa-walking float-right"></i></span>');
                        }
                    }
                    return option.text;
                }
            },
            form_submit: function(e) {
                var response = e.response;
                var modal = e.modal;
                var state = e.state; // can be 'beforeSend' or 'completed'
                var linker = e.linker;

                kingriders.Utils.isDebug() && console.log('loaded_data', e);

                if (state == 'beforeSend') {
                    /* request is not completed yet, we have form data available */
                    var data = {
                        id: response.name,
                        text: response.name
                    };

                    var newOption = new Option(data.text, data.id, false, true);
                    $('#kt-portlet__create-driver [name="client_id"]').append(newOption).trigger('change.select2');
                    newOption.setAttribute('data-ref', linker);

                } else if (state == "error") {
                    /* remove option from select */

                    $('#kt-portlet__create-driver select[name=client_id] option[data-select2-tag="true"]').remove();
                    var opt = $('#kt-portlet__create-driver [name=client_id] [data-ref="' + linker + '"]');
                    if (opt.length) {
                        opt.remove();
                    }
                    $('#kt-portlet__create-driver select[name=client_id]').val(null).trigger('change.select2');
                } else {
                    /* request might be completed and we have response from server */
                    var opt = $('#kt-portlet__create-driver [name=client_id] [data-ref="' + linker + '"]');
                    if (opt.length) {
                        /* change the id */
                        opt.val(response.id).removeAttr('data-ref');
                        $('#kt-portlet__create-driver [name="client_id"]').trigger('change.select2');
                    }

                }
            },
            form_loaded: function() {
                if (typeof CLIENT_MODULE !== "undefined") {
                    CLIENT_MODULE.Utils.reset_page();

                    /* add the client name */
                    var client_name = $('#kt-portlet__create-driver [name=client_id] [data-select2-tag]:last-child')
                        .text();
                    $(CLIENT_MODULE.container).find('[name="name"]').val(client_name);
                    setTimeout(function() {
                        $(CLIENT_MODULE.container).find('[name="name"]').focus();
                    }, 100);
                }
            },
            modal_closed: function(e) {
                /* modal was closed without adding data, we need to remove the tags */
                $('#kt-portlet__create-driver select[name=client_id] option[data-select2-tag="true"]').remove();
                $('#kt-portlet__create-driver select[name=client_id]').val(null).trigger('change.select2');
            }
        };

        $(function() {
            let input = document.querySelector('#kt-portlet__create-driver').querySelector('[name=phone_number]');
            const errorMsg = document.querySelector("#error-msg");
            const validMsg = document.querySelector("#valid-msg");
            // here, the index maps to the error code returned from getValidationError - see readme
            const errorMap = {'-99':'Invalid Number',0:"Invalid Number", 1:"Invalid Country Code", 2:"Too Short", 3:"Too Long", 4:"Invalid Number", 5:"Invalid Number"};
            const iti = window.intlTelInput(input, {
                utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js",
                hiddenInput: "full_phone",
                initialCountry: "AE"
            });
            const reset = () => {
                input.classList.remove("error");
                errorMsg.innerHTML = "";
                errorMsg.classList.add("hide");
                validMsg.classList.add("hide");
            };

            // on blur: validate
            input.addEventListener('blur', () => {
            reset();
            if (input.value.trim()) {
                if (iti.isValidNumber()) {
                    validMsg.classList.remove("hide");
                    // $('#submitbtn').prop("disabled", false);
                } else {
                    // $('#submitbtn').prop("disabled", true);
                    input.classList.add("error");
                    const errorCode = iti.getValidationError();
                    errorMsg.innerHTML = errorMap[errorCode];
                    errorMsg.classList.remove("hide");
                }
            }
            });

            // on keyup / change flag: reset
            input.addEventListener('change', reset);
            input.addEventListener('keyup', reset);
            $('#kt-portlet__create-driver [name=client_id]').on('change', function() {
                var selector = $(this);
                var selected = $(this).find(':selected');

                /* need to check if selected option is newly created tag or just random option */
                if (typeof selected.attr('data-select2-tag') !== "undefined" && selected.attr(
                        'data-select2-tag') == 'true') {
                    /* we need to show form to create this record */
                    var btn = $('#kt-portlet__create-driver [data-create-client]');
                    if (btn.length) {
                        btn.trigger('click');
                    }

                }
            });


            /* preload the kr-ajax module (only if laoded in modal) */
            var MODAL = $('#kt-portlet__create-driver').parents('.modal');
            if (MODAL.length) {
                setTimeout(function() {
                    $('#kt-portlet__create-driver ' + kingriders.Plugins.Selectors.kr_ajax_preload).each(
                        function(i, elem) {
                            /* initiate the ajax */
                            $(this).trigger('click.krevent', {
                                preload: true
                            });
                        });
                }, 100);
            }


            if (typeof KINGVIEW !== "undefined") {
                /* Seems page was loaded in OnAir, reset page */
                $('#kt-portlet__create-driver form').attr('action', $('#kt-portlet__create-driver form').attr(
                    'data-add')).find('[name=driver_id]').remove();
                if (typeof DRIVER_MODULE !== "undefined") DRIVER_MODULE.Utils.reset_page();
            }

            /* Check if page has config, do accordingly */
            @isset($config)
                /* This will help us in loading page as edit & view */
                @isset($config->driver)
                    var _DataLoaded = {!! $config->driver !!};
                    DRIVER_MODULE.Utils.load_page(_DataLoaded);
                @endisset
            @endisset

        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/intlTelInput.min.js"></script>
@endsection
