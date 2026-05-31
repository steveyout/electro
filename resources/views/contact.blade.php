@include('partials.header')
<!-- Single Page Header start -->
<div class="container-fluid page-header py-5">
    <h1 class="text-center text-white display-6 wow fadeInUp" data-wow-delay="0.1s">Contact Us</h1>
    <ol class="breadcrumb justify-content-center mb-0 wow fadeInUp" data-wow-delay="0.3s">
        <li class="breadcrumb-item"><a href="{{route('shop.home.contact')}}">Home</a></li>
        <li class="breadcrumb-item active text-white">Contact</li>
    </ol>
</div>
<!-- Single Page Header End -->

<!-- Contucts Start -->
<div class="container-fluid contact py-5">
    <div class="container py-5">
        <div class="p-5 bg-light rounded">
            <div class="row g-4">
                <div class="col-12">
                    <div class="text-center mx-auto wow fadeInUp" data-wow-delay="0.1s" style="max-width: 900px;">
                        <h4 class="text-primary border-bottom border-primary border-2 d-inline-block pb-2">Get in
                            touch</h4>
                        <p class="mb-5 fs-5 text-dark">We are here for you! how can we help, We are here for you!
                        </p>
                    </div>
                </div>
                <div class="col-lg-7">
                    <h5 class="text-primary wow fadeInUp" data-wow-delay="0.1s">Let’s Connect</h5>
                    <h1 class="display-5 mb-4 wow fadeInUp" data-wow-delay="0.3s">Send Your Message</h1>
                    <p class="mb-4 wow fadeInUp" data-wow-delay="0.5s">Contact us and we will get back to you.</p>
                    <form>
                        <div class="row g-4 wow fadeInUp" data-wow-delay="0.1s">
                            <div class="col-lg-12 col-xl-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="name" placeholder="Your Name">
                                    <label for="name">Your Name</label>
                                </div>
                            </div>
                            <div class="col-lg-12 col-xl-6">
                                <div class="form-floating">
                                    <input type="email" class="form-control" id="email" placeholder="Your Email">
                                    <label for="email">Your Email</label>
                                </div>
                            </div>
                            <div class="col-lg-12 col-xl-6">
                                <div class="form-floating">
                                    <input type="phone" class="form-control" id="phone" placeholder="Phone">
                                    <label for="phone">Your Phone</label>
                                </div>
                            </div>
                            <div class="col-lg-12 col-xl-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="project" placeholder="Project">
                                    <label for="project">Your Project</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="subject" placeholder="Subject">
                                    <label for="subject">Subject</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                        <textarea class="form-control" placeholder="Leave a message here" id="message"
                                                  style="height: 160px"></textarea>
                                    <label for="message">Message</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary w-100 py-3">Send Message</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-lg-5 wow fadeInUp" data-wow-delay="0.2s">
                    <div class="h-100 rounded">
                        <iframe class="rounded w-100" style="height: 100%;"
                                src="https://maps.google.com/maps?width=600&amp;height=400&amp;hl=en&amp;q=Bihi Towers&amp;t=&amp;z=14&amp;ie=UTF8&amp;iwloc=B&amp;output=embed"
                                loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<!-- Contuct End -->
@include('partials.footer')
