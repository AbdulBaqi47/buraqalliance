@extends('Tenant.layouts.app')

@section('page_title')
    Edit Driver
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
    <div class="kt-portlet mt-5" id="kt-portlet__edit-driver" kr-ajax-content>

        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">Edit Driver</h3>
            </div>
            <div class="kt-portlet__head-label" kr-ajax-closebtn></div>
        </div>
        <!--begin::Form-->
        <form class="kt-form" enctype="multipart/form-data" action="{{ route('tenant.admin.drivers.edit', $driver->id) }}"
            method="POST">
            @csrf
            <div class="kt-portlet__body">
                {{-- Type --}}
                <div class="form-group">
                    <label>Select Type <span class="text-danger">*<span></label>
                    <div class="kt-radio-inline">
                        <label class="kt-radio">
                            <input type="radio" value="rider" @if($driver->type=='rider') checked @endif name="type" required> Rider
                            <span></span>
                        </label>
                        <label class="kt-radio">
                            <input type="radio" value="driver"  @if($driver->type=='driver') checked @endif name="type" required> Driver
                            <span></span>
                        </label>
                    </div>
                    <span class="form-text text-muted">select Driver or Rider</span>
                </div>
                {{-- Name --}}
                <div class="form-group">
                    <label>Name <span class="text-danger">*<span></label>
                    <input type="text" autocomplete="off" name="name" required
                        class="form-control @error('name') is-invalid @enderror" placeholder="Enter Name"
                        value="{{ $driver->name }}">
                    @error('name')
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror

                </div>
                {{-- Email --}}
                <div class="form-group">
                    <label>Email <span class="text-danger">*<span></label>
                    <input type="email" autocomplete="off" name="email" required
                        class="form-control @error('email') is-invalid @enderror" placeholder="Enter Email"
                        value="{{ $driver->email }}">
                    @error('email')
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror

                </div>
                {{-- Date of Birth --}}
                <div class="form-group">
                    <label>Date of Birth </label>
                    <input autocomplete="off" class="kr-datepicker form-control @error('date_of_birth') is-invalid @enderror" type="search" name="date_of_birth" data-state="date" data-default="{{ $driver->date_of_birth }}">
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
                    <input type="text" autocomplete="off" name="phone_number" class="form-control @error('full_phone') is-invalid @enderror" placeholder="Enter Number" value="{{ old('full_phone') ?? $driver->phone_number }}">
                    <span id="valid-msg" class="hide">i.e. 055 123 4567</span>
                    <br/>
                    <span id="error-msg" class="hide text-danger"></span>
                    @error('full_phone')
                        </br/>
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ str_replace('full_phone','Phone Number',$message) }}</strong>
                        </span>
                    @enderror

                </div>
                {{-- Location --}}
                <div class="form-group">
                    <label>Location <span class="text-danger">*<span></label>
                    @include('Tenant.includes.locations', ['selected' => $driver->location ?? '' ])
                    @error('location')
                        </br/>
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror

                </div>
                {{-- Profile Picture --}}
                <div class="form-group">
                    <label>Uplaod Profile Picture  </label>
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="kt-uppy kr-uppy" uppy-size="20" uppy-max="1" uppy-min="1"
                                uppy-label="Profile Picture" uppy-input="profile_picture"></div>
                            <span class="form-text text-muted">Max file size is 20MB and max number of files is 1.</span>
                        </div>
                    </div>
                </div>
                {{-- License Number --}}
                <div class="form-group">
                    <label>License Number </label>
                    <input type="text" autocomplete="off" name="liscence_number"
                        class="form-control @error('liscence_number') is-invalid @enderror" placeholder="Enter Liscence"
                        value="{{ $driver->liscence_number }}">
                    @error('liscence_number')
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                {{-- License Images --}}
                <div class="form-group">
                    <label>Uplaod License Pictures </label>
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="kt-uppy kr-uppy" uppy-size="20" uppy-max="1" uppy-min="1"
                                uppy-label="Front Picture" uppy-input="liscence_pictures_front"></div>
                            <span class="form-text text-muted">Max file size is 20MB and max number of files is 1.</span>
                        </div>
                        <div>
                            <div class="kt-uppy kr-uppy" uppy-size="20" uppy-max="1" uppy-min="1"
                                uppy-label="Back Picture" uppy-input="liscence_pictures_back"></div>
                            <span class="form-text text-muted">Max file size is 20MB and max number of files is 1.</span>
                        </div>
                    </div>
                </div>
                {{-- License Expiry --}}
                <div class="form-group">
                    <label>License Expiry </label>
                    <input autocomplete="off" type="search" name="liscence_expiry" data-state="date"
                        class="kr-datepicker form-control @error('liscence_expiry') is-invalid @enderror"
                        data-default="{{ $driver->liscence_expiry }}">
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
                        value="{{ $driver->emirates_id_no }}">
                    @error('emirates_id_no')
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                {{-- Emirates ID Images --}}
                <div class="form-group">
                    <label>Uplaod Emirates ID Pictures </label>
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="kt-uppy kr-uppy" uppy-size="20" uppy-max="1" uppy-min="1"
                                uppy-label="Front Picture" uppy-input="emirates_id_pictures_front"></div>
                            <span class="form-text text-muted">Max file size is 20MB and max number of files is 1.</span>
                        </div>
                        <div>
                            <div class="kt-uppy kr-uppy" uppy-size="20" uppy-max="1" uppy-min="1"
                                uppy-label="Back Picture" uppy-input="emirates_id_pictures_back"></div>
                            <span class="form-text text-muted">Max file size is 20MB and max number of files is 1.</span>
                        </div>
                    </div>
                </div>
                {{-- Emirates ID Expiry --}}
                <div class="form-group">
                    <label>Emirates ID Expiry </label>
                    <input autocomplete="off" type="search" name="emirates_id_expiry" data-state="date"
                        class="kr-datepicker form-control @error('emirates_id_expiry') is-invalid @enderror"
                        data-default="{{ $driver->emirates_id_expiry }}">
                    @error('emirates_id_expiry')
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @else
                        <span class="form-text text-muted">Please enter emirates id expiry</span>
                    @enderror

                </div>
                {{-- Passport Number --}}
                <div class="form-group">
                    <label>Passport Number <span class="text-danger">*<span></label>
                    <input type="text" autocomplete="off" name="passport_number"
                        class="form-control @error('passport_number') is-invalid @enderror" required placeholder="Enter Passport"
                        value="{{ $driver->passport_number }}">
                    @error('passport_number')
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                {{-- Passport Images --}}
                <div class="form-group">
                    <label>Uplaod Passport Picture  </label>
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="kt-uppy kr-uppy" uppy-size="20" uppy-max="1" uppy-min="1"
                                uppy-label="Passport Picture" uppy-input="passport_pictures_front"></div>
                            <span class="form-text text-muted">Max file size is 20MB and max number of files is 1.</span>
                        </div>
                    </div>
                </div>
                {{-- Passport Expiry --}}
                <div class="form-group">
                    <label>Passport Expiry </label>
                    <input type="search" autocomplete="off" name="passport_expiry" data-state="date"
                        class="kr-datepicker form-control @error('passport_expiry') is-invalid @enderror"
                        data-default="{{ $driver->passport_expiry }}">
                    @error('passport_expiry')
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @else
                        <span class="form-text text-muted">Please enter passport expiry</span>
                    @enderror

                </div>
                {{-- Visa Images --}}
                <div class="form-group">
                    <label>Visa Picture </label>
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="kt-uppy kr-uppy" uppy-size="20" uppy-max="1" uppy-min="1"
                                uppy-label="VISA Picture" uppy-input="visa_pictures_front"></div>
                            <span class="form-text text-muted">Max file size is 20MB and max number of files is 1.</span>
                        </div>
                    </div>
                </div>
                {{-- Visa Expiry --}}
                <div class="form-group">
                    <label>Visa Expiry </label>
                    <input autocomplete="off" type="search" name="visa_expiry" data-state="date"
                        class="kr-datepicker form-control @error('visa_expiry') is-invalid @enderror"
                        data-default="{{ $driver->visa_expiry }}">
                    @error('visa_expiry')
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @else
                        <span class="form-text text-muted">Please enter visa expiry</span>
                    @enderror

                </div>
                {{-- RTA PERMIT Number --}}
                <div class="form-group">
                    <label>RTA Permit Number </label>
                    <input type="text" name="rta_permit_number"
                        class="form-control @error('rta_permit_number') is-invalid @enderror" placeholder="RTA Permit Number"
                        value="{{ $driver->rta_permit_number ?? '' }}">
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
                    <input type="search" autocomplete="off" name="rta_permit_expiry" data-state="date"
                        class="kr-datepicker form-control @error('rta_permit_expiry') is-invalid @enderror"
                        data-default="{{ $driver->rta_permit_expiry ?? '' }}">
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
                        <option @if ($driver->nationality === 'andorran') selected @endif value="andorran">Andorran</option>
                        <option @if ($driver->nationality === 'angolan') selected @endif value="angolan">Angolan</option>
                        <option @if ($driver->nationality === 'antiguans') selected @endif value="antiguans">Antiguans</option>
                        <option @if ($driver->nationality === 'argentinean') selected @endif value="argentinean">Argentinean
                        </option>
                        <option @if ($driver->nationality === 'afghan') selected @endif value="afghan">Afghan</option>
                        <option @if ($driver->nationality === 'albanian') selected @endif value="albanian">Albanian</option>
                        <option @if ($driver->nationality === 'algerian') selected @endif value="algerian">Algerian</option>
                        <option @if ($driver->nationality === 'american') selected @endif value="american">American</option>
                        <option @if ($driver->nationality === 'armenian') selected @endif value="armenian">Armenian</option>
                        <option @if ($driver->nationality === 'australian') selected @endif value="australian">Australian</option>
                        <option @if ($driver->nationality === 'austrian') selected @endif value="austrian">Austrian</option>
                        <option @if ($driver->nationality === 'azerbaijani') selected @endif value="azerbaijani">Azerbaijani
                        </option>
                        <option @if ($driver->nationality === 'bahamian') selected @endif value="bahamian">Bahamian</option>
                        <option @if ($driver->nationality === 'bahraini') selected @endif value="bahraini">Bahraini</option>
                        <option @if ($driver->nationality === 'bangladeshi') selected @endif value="bangladeshi">Bangladeshi
                        </option>
                        <option @if ($driver->nationality === 'barbadian') selected @endif value="barbadian">Barbadian</option>
                        <option @if ($driver->nationality === 'barbudans') selected @endif value="barbudans">Barbudans</option>
                        <option @if ($driver->nationality === 'batswana') selected @endif value="batswana">Batswana</option>
                        <option @if ($driver->nationality === 'belarusian') selected @endif value="belarusian">Belarusian</option>
                        <option @if ($driver->nationality === 'belgian') selected @endif value="belgian">Belgian</option>
                        <option @if ($driver->nationality === 'belizean') selected @endif value="belizean">Belizean</option>
                        <option @if ($driver->nationality === 'beninese') selected @endif value="beninese">Beninese</option>
                        <option @if ($driver->nationality === 'bhutanese') selected @endif value="bhutanese">Bhutanese</option>
                        <option @if ($driver->nationality === 'bolivian') selected @endif value="bolivian">Bolivian</option>
                        <option @if ($driver->nationality === 'bosnian') selected @endif value="bosnian">Bosnian</option>
                        <option @if ($driver->nationality === 'brazilian') selected @endif value="brazilian">Brazilian</option>
                        <option @if ($driver->nationality === 'british') selected @endif value="british">British</option>
                        <option @if ($driver->nationality === 'bruneian') selected @endif value="bruneian">Bruneian</option>
                        <option @if ($driver->nationality === 'bulgarian') selected @endif value="bulgarian">Bulgarian</option>
                        <option @if ($driver->nationality === 'burkinabe') selected @endif value="burkinabe">Burkinabe</option>
                        <option @if ($driver->nationality === 'burmese') selected @endif value="burmese">Burmese</option>
                        <option @if ($driver->nationality === 'burundian') selected @endif value="burundian">Burundian</option>
                        <option @if ($driver->nationality === 'cambodian') selected @endif value="cambodian">Cambodian</option>
                        <option @if ($driver->nationality === 'cameroonian') selected @endif value="cameroonian">Cameroonian
                        </option>
                        <option @if ($driver->nationality === 'canadian') selected @endif value="canadian">Canadian</option>
                        <option @if ($driver->nationality === 'cape') selected @endif value="cape verdean">Cape Verdean
                        </option>
                        <option @if ($driver->nationality === 'central') selected @endif value="central african">Central African
                        </option>
                        <option @if ($driver->nationality === 'chadian') selected @endif value="chadian">Chadian</option>
                        <option @if ($driver->nationality === 'chilean') selected @endif value="chilean">Chilean</option>
                        <option @if ($driver->nationality === 'chinese') selected @endif value="chinese">Chinese</option>
                        <option @if ($driver->nationality === 'colombian') selected @endif value="colombian">Colombian</option>
                        <option @if ($driver->nationality === 'comoran') selected @endif value="comoran">Comoran</option>
                        <option @if ($driver->nationality === 'congolese') selected @endif value="congolese">Congolese</option>
                        <option @if ($driver->nationality === 'costa') selected @endif value="costa rican">Costa Rican
                        </option>
                        <option @if ($driver->nationality === 'croatian') selected @endif value="croatian">Croatian</option>
                        <option @if ($driver->nationality === 'cuban') selected @endif value="cuban">Cuban</option>
                        <option @if ($driver->nationality === 'cypriot') selected @endif value="cypriot">Cypriot</option>
                        <option @if ($driver->nationality === 'czech') selected @endif value="czech">Czech</option>
                        <option @if ($driver->nationality === 'danish') selected @endif value="danish">Danish</option>
                        <option @if ($driver->nationality === 'djibouti') selected @endif value="djibouti">Djibouti</option>
                        <option @if ($driver->nationality === 'dominican') selected @endif value="dominican">Dominican</option>
                        <option @if ($driver->nationality === 'dutch') selected @endif value="dutch">Dutch</option>
                        <option @if ($driver->nationality === 'east') selected @endif value="east timorese">East Timorese
                        </option>
                        <option @if ($driver->nationality === 'ecuadorean') selected @endif value="ecuadorean">Ecuadorean</option>
                        <option @if ($driver->nationality === 'egyptian') selected @endif value="egyptian">Egyptian</option>
                        <option @if ($driver->nationality === 'emirian') selected @endif value="emirian">Emirian</option>
                        <option @if ($driver->nationality === 'equatorial') selected @endif value="equatorial guinean">Equatorial
                            Guinean</option>
                        <option @if ($driver->nationality === 'eritrean') selected @endif value="eritrean">Eritrean</option>
                        <option @if ($driver->nationality === 'estonian') selected @endif value="estonian">Estonian</option>
                        <option @if ($driver->nationality === 'ethiopian') selected @endif value="ethiopian">Ethiopian</option>
                        <option @if ($driver->nationality === 'fijian') selected @endif value="fijian">Fijian</option>
                        <option @if ($driver->nationality === 'filipino') selected @endif value="filipino">Filipino</option>
                        <option @if ($driver->nationality === 'finnish') selected @endif value="finnish">Finnish</option>
                        <option @if ($driver->nationality === 'french') selected @endif value="french">French</option>
                        <option @if ($driver->nationality === 'gabonese') selected @endif value="gabonese">Gabonese</option>
                        <option @if ($driver->nationality === 'gambian') selected @endif value="gambian">Gambian</option>
                        <option @if ($driver->nationality === 'georgian') selected @endif value="georgian">Georgian</option>
                        <option @if ($driver->nationality === 'german') selected @endif value="german">German</option>
                        <option @if ($driver->nationality === 'ghanaian') selected @endif value="ghanaian">Ghanaian</option>
                        <option @if ($driver->nationality === 'greek') selected @endif value="greek">Greek</option>
                        <option @if ($driver->nationality === 'grenadian') selected @endif value="grenadian">Grenadian</option>
                        <option @if ($driver->nationality === 'guatemalan') selected @endif value="guatemalan">Guatemalan</option>
                        <option @if ($driver->nationality === 'guinea') selected @endif value="guinea-bissauan">Guinea-Bissauan
                        </option>
                        <option @if ($driver->nationality === 'guinean') selected @endif value="guinean">Guinean</option>
                        <option @if ($driver->nationality === 'guyanese') selected @endif value="guyanese">Guyanese</option>
                        <option @if ($driver->nationality === 'haitian') selected @endif value="haitian">Haitian</option>
                        <option @if ($driver->nationality === 'herzegovinian') selected @endif value="herzegovinian">Herzegovinian
                        </option>
                        <option @if ($driver->nationality === 'honduran') selected @endif value="honduran">Honduran</option>
                        <option @if ($driver->nationality === 'hungarian') selected @endif value="hungarian">Hungarian</option>
                        <option @if ($driver->nationality === 'icelander') selected @endif value="icelander">Icelander</option>
                        <option @if ($driver->nationality === 'indian') selected @endif value="indian">Indian</option>
                        <option @if ($driver->nationality === 'indonesian') selected @endif value="indonesian">Indonesian</option>
                        <option @if ($driver->nationality === 'iranian') selected @endif value="iranian">Iranian</option>
                        <option @if ($driver->nationality === 'iraqi') selected @endif value="iraqi">Iraqi</option>
                        <option @if ($driver->nationality === 'irish') selected @endif value="irish">Irish</option>
                        <option @if ($driver->nationality === 'israeli') selected @endif value="israeli">Israeli</option>
                        <option @if ($driver->nationality === 'italian') selected @endif value="italian">Italian</option>
                        <option @if ($driver->nationality === 'ivorian') selected @endif value="ivorian">Ivorian</option>
                        <option @if ($driver->nationality === 'jamaican') selected @endif value="jamaican">Jamaican</option>
                        <option @if ($driver->nationality === 'japanese') selected @endif value="japanese">Japanese</option>
                        <option @if ($driver->nationality === 'jordanian') selected @endif value="jordanian">Jordanian</option>
                        <option @if ($driver->nationality === 'kazakhstani') selected @endif value="kazakhstani">Kazakhstani
                        </option>
                        <option @if ($driver->nationality === 'kenyan') selected @endif value="kenyan">Kenyan</option>
                        <option @if ($driver->nationality === 'kittian') selected @endif value="kittian and nevisian">Kittian
                            and Nevisian</option>
                        <option @if ($driver->nationality === 'kuwaiti') selected @endif value="kuwaiti">Kuwaiti</option>
                        <option @if ($driver->nationality === 'kyrgyz') selected @endif value="kyrgyz">Kyrgyz</option>
                        <option @if ($driver->nationality === 'laotian') selected @endif value="laotian">Laotian</option>
                        <option @if ($driver->nationality === 'latvian') selected @endif value="latvian">Latvian</option>
                        <option @if ($driver->nationality === 'lebanese') selected @endif value="lebanese">Lebanese</option>
                        <option @if ($driver->nationality === 'liberian') selected @endif value="liberian">Liberian</option>
                        <option @if ($driver->nationality === 'libyan') selected @endif value="libyan">Libyan</option>
                        <option @if ($driver->nationality === 'liechtensteiner') selected @endif value="liechtensteiner">
                            Liechtensteiner</option>
                        <option @if ($driver->nationality === 'lithuanian') selected @endif value="lithuanian">Lithuanian
                        </option>
                        <option @if ($driver->nationality === 'luxembourger') selected @endif value="luxembourger">Luxembourger
                        </option>
                        <option @if ($driver->nationality === 'macedonian') selected @endif value="macedonian">Macedonian
                        </option>
                        <option @if ($driver->nationality === 'malagasy') selected @endif value="malagasy">Malagasy</option>
                        <option @if ($driver->nationality === 'malawian') selected @endif value="malawian">Malawian</option>
                        <option @if ($driver->nationality === 'malaysian') selected @endif value="malaysian">Malaysian</option>
                        <option @if ($driver->nationality === 'maldivan') selected @endif value="maldivan">Maldivan</option>
                        <option @if ($driver->nationality === 'malian') selected @endif value="malian">Malian</option>
                        <option @if ($driver->nationality === 'maltese') selected @endif value="maltese">Maltese</option>
                        <option @if ($driver->nationality === 'marshallese') selected @endif value="marshallese">Marshallese
                        </option>
                        <option @if ($driver->nationality === 'mauritanian') selected @endif value="mauritanian">Mauritanian
                        </option>
                        <option @if ($driver->nationality === 'mauritian') selected @endif value="mauritian">Mauritian</option>
                        <option @if ($driver->nationality === 'mexican') selected @endif value="mexican">Mexican</option>
                        <option @if ($driver->nationality === 'micronesian') selected @endif value="micronesian">Micronesian
                        </option>
                        <option @if ($driver->nationality === 'moldovan') selected @endif value="moldovan">Moldovan</option>
                        <option @if ($driver->nationality === 'monacan') selected @endif value="monacan">Monacan</option>
                        <option @if ($driver->nationality === 'mongolian') selected @endif value="mongolian">Mongolian</option>
                        <option @if ($driver->nationality === 'moroccan') selected @endif value="moroccan">Moroccan</option>
                        <option @if ($driver->nationality === 'mosotho') selected @endif value="mosotho">Mosotho</option>
                        <option @if ($driver->nationality === 'motswana') selected @endif value="motswana">Motswana</option>
                        <option @if ($driver->nationality === 'mozambican') selected @endif value="mozambican">Mozambican
                        </option>
                        <option @if ($driver->nationality === 'namibian') selected @endif value="namibian">Namibian</option>
                        <option @if ($driver->nationality === 'nauruan') selected @endif value="nauruan">Nauruan</option>
                        <option @if ($driver->nationality === 'nepalese') selected @endif value="nepalese">Nepalese</option>
                        <option @if ($driver->nationality === 'new') selected @endif value="new zealander">New Zealander
                        </option>
                        <option @if ($driver->nationality === 'ni') selected @endif value="ni-vanuatu">Ni-Vanuatu
                        </option>
                        <option @if ($driver->nationality === 'nicaraguan') selected @endif value="nicaraguan">Nicaraguan
                        </option>
                        <option @if ($driver->nationality === 'nigerien') selected @endif value="nigerien">Nigerien</option>
                        <option @if ($driver->nationality === 'north') selected @endif value="north korean">North Korean
                        </option>
                        <option @if ($driver->nationality === 'northern') selected @endif value="northern irish">Northern Irish
                        </option>
                        <option @if ($driver->nationality === 'norwegian') selected @endif value="norwegian">Norwegian</option>
                        <option @if ($driver->nationality === 'omani') selected @endif value="omani">Omani</option>
                        <option @if ($driver->nationality === 'pakistani') selected @endif value="pakistani">Pakistani</option>
                        <option @if ($driver->nationality === 'palauan') selected @endif value="palauan">Palauan</option>
                        <option @if ($driver->nationality === 'panamanian') selected @endif value="panamanian">Panamanian
                        </option>
                        <option @if ($driver->nationality === 'papua') selected @endif value="papua new guinean">Papua New
                            Guinean</option>
                        <option @if ($driver->nationality === 'paraguayan') selected @endif value="paraguayan">Paraguayan
                        </option>
                        <option @if ($driver->nationality === 'peruvian') selected @endif value="peruvian">Peruvian</option>
                        <option @if ($driver->nationality === 'polish') selected @endif value="polish">Polish</option>
                        <option @if ($driver->nationality === 'portuguese') selected @endif value="portuguese">Portuguese
                        </option>
                        <option @if ($driver->nationality === 'qatari') selected @endif value="qatari">Qatari</option>
                        <option @if ($driver->nationality === 'romanian') selected @endif value="romanian">Romanian</option>
                        <option @if ($driver->nationality === 'russian') selected @endif value="russian">Russian</option>
                        <option @if ($driver->nationality === 'rwandan') selected @endif value="rwandan">Rwandan</option>
                        <option @if ($driver->nationality === 'saint') selected @endif value="saint lucian">Saint Lucian
                        </option>
                        <option @if ($driver->nationality === 'salvadoran') selected @endif value="salvadoran">Salvadoran
                        </option>
                        <option @if ($driver->nationality === 'samoan') selected @endif value="samoan">Samoan</option>
                        <option @if ($driver->nationality === 'san') selected @endif value="san marinese">San Marinese
                        </option>
                        <option @if ($driver->nationality === 'sao') selected @endif value="sao tomean">Sao Tomean
                        </option>
                        <option @if ($driver->nationality === 'saudi') selected @endif value="saudi">Saudi</option>
                        <option @if ($driver->nationality === 'scottish') selected @endif value="scottish">Scottish</option>
                        <option @if ($driver->nationality === 'senegalese') selected @endif value="senegalese">Senegalese
                        </option>
                        <option @if ($driver->nationality === 'serbian') selected @endif value="serbian">Serbian</option>
                        <option @if ($driver->nationality === 'seychellois') selected @endif value="seychellois">Seychellois
                        </option>
                        <option @if ($driver->nationality === 'sierra') selected @endif value="sierra leonean">Sierra Leonean
                        </option>
                        <option @if ($driver->nationality === 'singaporean') selected @endif value="singaporean">Singaporean
                        </option>
                        <option @if ($driver->nationality === 'slovakian') selected @endif value="slovakian">Slovakian</option>
                        <option @if ($driver->nationality === 'slovenian') selected @endif value="slovenian">Slovenian</option>
                        <option @if ($driver->nationality === 'solomon') selected @endif value="solomon islander">Solomon
                            Islander</option>
                        <option @if ($driver->nationality === 'somali') selected @endif value="somali">Somali</option>
                        <option @if ($driver->nationality === 'south') selected @endif value="south african">South African
                        </option>
                        <option @if ($driver->nationality === 'south') selected @endif value="south korean">South Korean
                        </option>
                        <option @if ($driver->nationality === 'spanish') selected @endif value="spanish">Spanish</option>
                        <option @if ($driver->nationality === 'sri') selected @endif value="sri lankan">Sri Lankan
                        </option>
                        <option @if ($driver->nationality === 'sudanese') selected @endif value="sudanese">Sudanese</option>
                        <option @if ($driver->nationality === 'surinamer') selected @endif value="surinamer">Surinamer</option>
                        <option @if ($driver->nationality === 'swazi') selected @endif value="swazi">Swazi</option>
                        <option @if ($driver->nationality === 'swedish') selected @endif value="swedish">Swedish</option>
                        <option @if ($driver->nationality === 'swiss') selected @endif value="swiss">Swiss</option>
                        <option @if ($driver->nationality === 'syrian') selected @endif value="syrian">Syrian</option>
                        <option @if ($driver->nationality === 'taiwanese') selected @endif value="taiwanese">Taiwanese</option>
                        <option @if ($driver->nationality === 'tajik') selected @endif value="tajik">Tajik</option>
                        <option @if ($driver->nationality === 'tanzanian') selected @endif value="tanzanian">Tanzanian</option>
                        <option @if ($driver->nationality === 'thai') selected @endif value="thai">Thai</option>
                        <option @if ($driver->nationality === 'togolese') selected @endif value="togolese">Togolese</option>
                        <option @if ($driver->nationality === 'tongan') selected @endif value="tongan">Tongan</option>
                        <option @if ($driver->nationality === 'trinidadian') selected @endif value="trinidadian or tobagonian">
                            Trinidadian or Tobagonian</option>
                        <option @if ($driver->nationality === 'tunisian') selected @endif value="tunisian">Tunisian</option>
                        <option @if ($driver->nationality === 'turkish') selected @endif value="turkish">Turkish</option>
                        <option @if ($driver->nationality === 'tuvaluan') selected @endif value="tuvaluan">Tuvaluan</option>
                        <option @if ($driver->nationality === 'ugandan') selected @endif value="ugandan">Ugandan</option>
                        <option @if ($driver->nationality === 'ukrainian') selected @endif value="ukrainian">Ukrainian</option>
                        <option @if ($driver->nationality === 'uruguayan') selected @endif value="uruguayan">Uruguayan</option>
                        <option @if ($driver->nationality === 'uzbekistani') selected @endif value="uzbekistani">Uzbekistani
                        </option>
                        <option @if ($driver->nationality === 'venezuelan') selected @endif value="venezuelan">Venezuelan
                        </option>
                        <option @if ($driver->nationality === 'vietnamese') selected @endif value="vietnamese">Vietnamese
                        </option>
                        <option @if ($driver->nationality === 'welsh') selected @endif value="welsh">Welsh</option>
                        <option @if ($driver->nationality === 'yemenite') selected @endif value="yemenite">Yemenite</option>
                        <option @if ($driver->nationality === 'zambian') selected @endif value="zambian">Zambian</option>
                        <option @if ($driver->nationality === 'zimbabwean') selected @endif value="zimbabwean">Zimbabwean
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
                                <input type="checkbox" {{ $driver->is_pasport_collected ? 'checked' : '' }}
                                    name="is_pasport_collected">
                                <span></span>
                            </label>
                        </span>
                    </div>
                </div>
                {{-- Additional Information --}}
                <div class="form-group form-group-last">
                    <label for="additional_details">Additional Details</label>
                    <textarea class="form-control" id="additional_details" name="additional_details" rows="3">{{$driver->additional_details}}</textarea>
                </div>
            </div>
            <div class="kt-portlet__foot kt-portlet__foot--solid">
                <div class="kt-form__actions kt-form__actions--right">
                    <button id="submitEditButton" type="submit" class="btn btn-brand">Save</button>
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
    <script type="text/javascript">

        $(document).ready(function(){
            let driver = {!! $driver !!};
            const addFileIfPresent = (property, input) => {
                if (property) {
                    kingriders.Plugins.uppy.addFile($(DRIVER_MODULE.container_el_name + ` form [uppy-input="${input}"]`).attr('id'), property);
                }
            };
            addFileIfPresent(driver.profile_picture, 'profile_picture');
            addFileIfPresent(driver.liscence_pictures?.front, 'liscence_pictures_front');
            addFileIfPresent(driver.liscence_pictures?.back, 'liscence_pictures_back');
            addFileIfPresent(driver.emirates_id_pictures?.front, 'emirates_id_pictures_front');
            addFileIfPresent(driver.emirates_id_pictures?.back, 'emirates_id_pictures_back');
            addFileIfPresent(driver.passport_pictures?.front, 'passport_pictures_front');
            addFileIfPresent(driver.visa_pictures?.front, 'visa_pictures_front');
            addFileIfPresent(driver.rta_permit_pictures?.front, 'rta_permit_pictures_front');
            addFileIfPresent(driver.rta_permit_pictures?.back, 'rta_permit_pictures_back');

        })
        var DRIVER_MODULE = {
            container_el_name: '#kt-portlet__edit-driver',
            container: $('#kt-portlet__edit-driver'),
            Utils: {

                reset_page: function() {
                    $('#kt-portlet__edit-driver form [name=driver_id]').remove();
                    /* clear the items */
                    $('#kt-portlet__edit-driver [name="client_id"]').val(null).trigger('change.select2');
                    $('#kt-portlet__edit-driver [name="plate"]').val(null);
                    $('#kt-portlet__edit-driver [name="model"]').val(null).trigger('change.select2');
                    $('#kt-portlet__edit-driver [name="manufacturer"]').val(null).trigger('change.select2');
                    $('#kt-portlet__edit-driver [name="cc"]').val(null);
                },
                load_page: function(driver) {
                    /* Load the job in page (this funtion is using in view job page) */

                    /* Update url */
                    var MODAL = $('#kt-portlet__edit-driver').parents('.modal');
                    if (MODAL.length) {
                        kingriders.Plugins.KR_AJAX.ModalOnAir.update({
                            modal: MODAL,
                            url: "{{ url('admin/drivers/edit') }}/" + driver._id,
                            title: 'Edit driver | Administrator'
                        });
                    }

                    /* need to check if job is suitable for edit, (not in creating process) */
                    if (driver.actions.status == 1) {
                        /* check if page if loaded in modal */
                        var MODAL = $('#kt-portlet__edit-driver').parents('.modal');
                        if (MODAL.length) {
                            MODAL.modal('show');
                        }

                        /* change the action of form to edit */
                        $('#kt-portlet__edit-driver form [name=driver_id]').remove();
                        $('#kt-portlet__edit-driver form').attr('action', $('#kt-portlet__edit-driver form')
                                .attr(
                                    'data-edit'))
                            .prepend('<input type="hidden" name="driver_id" value="' + driver._id + '" />');

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
                    $('#kt-portlet__edit-driver [name="client_id"]').append(newOption).trigger('change.select2');
                    newOption.setAttribute('data-ref', linker);

                } else if (state == "error") {
                    /* remove option from select */

                    $('#kt-portlet__edit-driver select[name=client_id] option[data-select2-tag="true"]').remove();
                    var opt = $('#kt-portlet__edit-driver [name=client_id] [data-ref="' + linker + '"]');
                    if (opt.length) {
                        opt.remove();
                    }
                    $('#kt-portlet__edit-driver select[name=client_id]').val(null).trigger('change.select2');
                } else {
                    /* request might be completed and we have response from server */
                    var opt = $('#kt-portlet__edit-driver [name=client_id] [data-ref="' + linker + '"]');
                    if (opt.length) {
                        /* change the id */
                        opt.val(response.id).removeAttr('data-ref');
                        $('#kt-portlet__edit-driver [name="client_id"]').trigger('change.select2');
                    }

                }
            },
            form_loaded: function() {
                if (typeof DRIVER_MODULE !== "undefined") {
                    DRIVER_MODULE.Utils.reset_page();

                    /* add the client name */
                    var client_name = $('#kt-portlet__edit-driver [name=client_id] [data-select2-tag]:last-child')
                        .text();
                    $(DRIVER_MODULE.container).find('[name="name"]').val(client_name);
                    setTimeout(function() {
                        $(DRIVER_MODULE.container).find('[name="name"]').focus();
                    }, 100);
                }
            },
            modal_closed: function(e) {
                /* modal was closed without adding data, we need to remove the tags */
                $('#kt-portlet__edit-driver select[name=client_id] option[data-select2-tag="true"]').remove();
                $('#kt-portlet__edit-driver select[name=client_id]').val(null).trigger('change.select2');
            }
        };

        $(function() {
            let input = document.querySelector('#kt-portlet__edit-driver').querySelector('[name=phone_number]');
            const errorMsg = document.querySelector("#error-msg");
            const validMsg = document.querySelector("#valid-msg");
            // here, the index maps to the error code returned from getValidationError - see readme
            const errorMap = {'-99':'Invalid Number',0:"Invalid Number", 1:"Invalid Country Code", 2:"Too Short", 3:"Too Long", 4:"Invalid Number", 5:"Invalid Number"};
            const iti = window.intlTelInput(input, {
                utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js",
                hiddenInput: "full_phone",
                initialCountry: "AE",
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
                    // $('#submitEditButton').prop("disabled", false);
                } else {
                    // $('#submitEditButton').prop("disabled", true);
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


            /* preload the kr-ajax module (only if laoded in modal) */
            var MODAL = $('#kt-portlet__edit-driver').parents('.modal');
            if (MODAL.length) {
                setTimeout(function() {
                    $('#kt-portlet__edit-driver ' + kingriders.Plugins.Selectors.kr_ajax_preload).each(
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
                $('#kt-portlet__edit-driver form').attr('action', $('#kt-portlet__edit-driver form').attr(
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
