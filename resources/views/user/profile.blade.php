@extends('layouts.app')

@section('content')

    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->has('picture'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle-fill me-2"></i>{{ $errors->first('picture') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="user-profile">
            <div class="row">
                <!-- user profile header start-->
                <div class="col-sm-12">
                    <div class="card profile-header bg-size"
                        style="background-image: url({{ asset('assets/images/profile.jpg') }}); background-size: cover; background-position: center center; display: block;">
                        <div class="profile-img-wrrap bg-size"
                            style="background-image: url(&quot;../assets/images/user-profile/bg-profile.jpg&quot;); background-size: cover; background-position: center center; display: block;">
                            <img class="img-fluid bg-img-cover" src="../assets/images/user-profile/bg-profile.jpg" alt=""
                                style="display: none;">
                        </div>
                        <div class="userpro-box">
                            <div class="img-wrraper">
                                <div class="avatar">
                                    <img class="img-fluid" alt="Avatar" src="{{ Auth::user()->picture ? asset(Auth::user()->picture) : asset('assets/images/lamine.webp') }}" style="width: 100px; height: 100px; object-fit: cover;">
                                </div>
                                <form id="profile-picture-form" action="{{ route('profile.update_picture') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PATCH')
                                    <input type="file" id="profile-picture-input" name="picture" accept="image/*" style="display: none;" onchange="document.getElementById('profile-picture-form').submit();">
                                </form>
                                <a class="icon-wrapper" href="javascript:void(0);" onclick="document.getElementById('profile-picture-input').click();">
                                    <i class="icofont icofont-pencil-alt-5"></i>
                                </a>
                            </div>
                            <div class="user-designation">
                                <div class="title"><a target="_blank" href="">
                                        <h4>{{ Auth::user()->full_name }}</h4>
                                        <h6>{{ Auth::user()->username }}</h6>
                                    </a></div>
                                <div class="social-media">
                                    <ul class="user-list-social">
                                        <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                                        <li><a href="#"><i class="fa fa-google-plus"></i></a></li>
                                        <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                                        <li><a href="#"><i class="fa fa-instagram"></i></a></li>
                                        <li><a href="#"><i class="fa fa-rss"></i></a></li>
                                    </ul>
                                </div>
                                <div class="follow">
                                    <ul class="follow-list">
                                        <li>
                                            <div class="follow-num counter">XXX</div><span>Follower</span>
                                        </li>
                                        <li>
                                            <div class="follow-num counter">XXX</div><span>Following</span>
                                        </li>
                                        <li>
                                            <div class="follow-num counter">XXX</div><span>Likes</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- user profile header end-->
                <div class="col-xl-3 col-lg-12 col-md-5 xl-35">
                    <div class="default-according style-1 faq-accordion job-accordion">
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="p-0">
                                            <button class="btn btn-link ps-0" data-bs-toggle="collapse"
                                                data-bs-target="#collapseicon2" aria-expanded="true"
                                                aria-controls="collapseicon2">About Me</button>
                                        </h5>
                                    </div>
                                    <div class="collapse show" id="collapseicon2" aria-labelledby="collapseicon2"
                                        data-parent="#accordion" style="">
                                        <div class="card-body post-about">
                                            <ul>
                                                <li>
                                                    <div class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                            height="24" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round" class="feather feather-briefcase">
                                                            <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                                                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                                                        </svg></div>
                                                    <div>
                                                        <h5>UX desginer at Pixelstrap</h5>
                                                        <p>banglore - 2024</p>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                            height="24" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round" class="feather feather-book">
                                                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                                            <path
                                                                d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z">
                                                            </path>
                                                        </svg></div>
                                                    <div>
                                                        <h5>studied computer science</h5>
                                                        <p>at london univercity - 2024</p>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                            height="24" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round" class="feather feather-heart">
                                                            <path
                                                                d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z">
                                                            </path>
                                                        </svg></div>
                                                    <div>
                                                        <h5>relationship status</h5>
                                                        <p>single</p>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                            height="24" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round" class="feather feather-map-pin">
                                                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                                            <circle cx="12" cy="10" r="3"></circle>
                                                        </svg></div>
                                                    <div>
                                                        <h5>lived in london</h5>
                                                        <p>last 5 year</p>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                            height="24" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round" class="feather feather-droplet">
                                                            <path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"></path>
                                                        </svg></div>
                                                    <div>
                                                        <h5>blood group</h5>
                                                        <p>O+ positive</p>
                                                    </div>
                                                </li>
                                            </ul>
                                            <div class="social-network theme-form"><span class="f-w-600">Social
                                                    Networks</span>
                                                <button class="btn social-btn btn-fb mb-2 text-center"><i
                                                        class="fa fa-facebook m-r-5"></i>Facebook</button>
                                                <button class="btn social-btn btn-twitter mb-2 text-center"><i
                                                        class="fa fa-twitter m-r-5"></i>Twitter</button>
                                                <button class="btn social-btn btn-google text-center"><i
                                                        class="fa fa-dribbble m-r-5"></i>Dribbble</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-9 col-lg-12 col-md-7 xl-65">
                    <div class="row">
                        <!-- profile post start-->
                        <div class="col-sm-12">
                            <div class="card">
                                <div class="profile-post">
                                    <div class="post-header">
                                        <div class="media">
                                            <img class="img-thumbnail rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;"
                                                src="{{ Auth::user()->picture ? asset(Auth::user()->picture) : asset('assets/images/lamine.webp') }}" alt="Generic placeholder image">
                                            <div class="media-body align-self-center"><a href="social-app.html">
                                                    <h5 class="user-name">{{ Auth::user()->full_name }}</h5>
                                                </a>
                                                <h6>22 Hours ago</h6>
                                            </div>
                                        </div>
                                        <div class="post-setting"><i class="fa fa-ellipsis-h"></i></div>
                                    </div>
                                    <div class="post-body">
                                        <div class="img-container">
                                            <div class="my-gallery" id="aniimated-thumbnials" itemscope=""
                                                data-pswp-uid="1">
                                                <figure itemprop="associatedMedia" itemscope=""><a
                                                        href="{{ asset('assets/images/profile.jpg') }}" itemprop="contentUrl"
                                                        data-size="1600x950"><img class="img-fluid rounded"
                                                            src="{{ asset('assets/images/profile.jpg') }}"
                                                            itemprop="thumbnail" alt="gallery"></a>
                                                    <figcaption itemprop="caption description">Wholesale Electric Asia Majuuuuuu!!</figcaption>
                                                </figure>
                                            </div>
                                        </div>
                                        <p>Dressing is a way of life. My customers are successful working women. I want
                                            people
                                            to be
                                            afraid of the women I dress. Age is something only in your head or a stereotype.
                                            Age
                                            means nothing when you are passionate about something. There has to be a balance
                                            between
                                            your mental satisfaction and the financial needs of your company.</p>
                                        <ul class="post-comment">
                                            <li>
                                                <label><a href="#"><svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                            height="24" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round" class="feather feather-heart">
                                                            <path
                                                                d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z">
                                                            </path>
                                                        </svg>&nbsp;&nbsp;Like<span class="counter">6</span></a></label>
                                            </li>
                                            <li>
                                                <label><a href="#"><svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                            height="24" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round" class="feather feather-message-square">
                                                            <path
                                                                d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z">
                                                            </path>
                                                        </svg>&nbsp;&nbsp;Comment<span class="counter">8</span></a></label>
                                            </li>
                                            <li>
                                                <label><a href="#"><svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                            height="24" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round" class="feather feather-share">
                                                            <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"></path>
                                                            <polyline points="16 6 12 2 8 6"></polyline>
                                                            <line x1="12" y1="2" x2="12" y2="15"></line>
                                                        </svg>&nbsp;&nbsp;share<span class="counter">1</span></a></label>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection