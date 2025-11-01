<!-- Font Awesome -->
<!--===============================================================================================-->
<link rel="stylesheet" type="text/css" href="{{ asset('fonts/font-awesome-4.7.0/css/font-awesome.min.css') }}">
<!--===============================================================================================-->

<!-- Footer -->
<footer class="bg3 p-t-75 p-b-32">
    <div class="container">
        <div class="row">
            <div class="col-sm-6 col-lg-3 p-b-50">
                <h4 class="stext-301 cl0 p-b-30">
                    Categories
                </h4>

                <ul>
                    @foreach($categories as $category) <!-- Loop untuk menampilkan kategori -->
                        <li class="p-b-10">
                            <a href="{{ route('user.categories.products', $category->id) }}" class="stext-107 cl7 hov-cl1 trans-04">
                                {{ $category->category_name }} <!-- Nama kategori ditampilkan disini -->
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="col-sm-6 col-lg-3 p-b-50">
                <h4 class="stext-301 cl0 p-b-30">
                    Help
                </h4>

                <ul>
                    <li class="p-b-10">
                        <a href="#" class="stext-107 cl7 hov-cl1 trans-04">
                            Track Order
                        </a>
                    </li>

                    <li class="p-b-10">
                        <a href="#" class="stext-107 cl7 hov-cl1 trans-04">
                            Returns
                        </a>
                    </li>

                    <li class="p-b-10">
                        <a href="#" class="stext-107 cl7 hov-cl1 trans-04">
                            Shipping
                        </a>
                    </li>

                    <li class="p-b-10">
                        <a href="#" class="stext-107 cl7 hov-cl1 trans-04">
                            FAQs
                        </a>
                    </li>
                </ul>
            </div>

            <div class="col-sm-6 col-lg-3 p-b-50">
                <h4 class="stext-301 cl0 p-b-30">
                    GET IN TOUCH
                </h4>

                <p class="stext-107 cl7 size-201">
                    Have questions? Contact us at our store on the 8th floor, 379 Hudson St, New York, NY 10018 or call us at (+62) 882-1532-8028.
                </p>

                <div class="p-t-27">
                    <a href="https://www.instagram.com/drive.venture/" class="fs-18 cl7 hov-cl1 trans-04 m-r-16">
                        <i class="fa fa-instagram"></i>
                    </a>

                    <a href="https://www.tiktok.com/@drive.venture" class="fs-18 cl7 hov-cl1 trans-04 m-r-16" target="_blank" rel="noopener">
                        <i class="fa-brands fa-tiktok"></i>
                    </a>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3 p-b-50">
                <h4 class="stext-301 cl0 p-b-30">
                    Newsletter
                </h4>

                <form>
                    <div class="wrap-input1 w-full p-b-4">
                        <input class="input1 bg-none plh1 stext-107 cl7" type="text" name="email"
                            placeholder="email@example.com">
                        <div class="focus-input1 trans-04"></div>
                    </div>

                    <div class="p-t-18">
                        <button class="flex-c-m stext-101 cl0 size-103 bg1 bor1 hov-btn2 p-lr-15 trans-04">
                            Subscribe
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="p-t-40">
            <div class="flex-c-m flex-w p-b-18">
                <a href="#" class="m-all-1">
                    <img src={{ asset('images/icons/icon-pay-01.png') }} alt="ICON-PAY">
                </a>

                <a href="#" class="m-all-1">
                    <img src={{ asset('images/icons/icon-pay-02.png') }} alt="ICON-PAY">
                </a>

                <a href="#" class="m-all-1">
                    <img src={{ asset('images/icons/icon-pay-03.png') }} alt="ICON-PAY">
                </a>

                <a href="#" class="m-all-1">
                    <img src={{ asset('images/icons/icon-pay-04.png') }} alt="ICON-PAY">
                </a>

                <a href="#" class="m-all-1">
                    <img src={{ asset('images/icons/icon-pay-05.png') }} alt="ICON-PAY">
                </a>
            </div>

            <p class="stext-107 cl6 txt-center">
                <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
                Copyright &copy;
                <script>
                    document.write(new Date().getFullYear());
                </script> All rights reserved | Teuku Aditya

            </p>
        </div>
    </div>
</footer>
