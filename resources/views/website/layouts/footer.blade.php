<footer class="footer text-white">
  <div class="footer__upper">
    <div class="section-container row">
      <!-- Logo & Description -->
      <div class="col-md-6 col-lg-3 mb-5 mb-lg-0">
        <div class="footer__logo">
           <img src="{{asset('assets/images/logo4.png')}}" alt="Bookstore Logo" style="height: 150px; width: auto;">
        </div>
        <p class="my-3 text-gray">نحن من أكبر المتاجر الإلكترونية المتخصصة في بيع الكتب بأنواعها وتوصيلها حتى باب المنزل.</p>
        <div class="footer__social d-flex gap-3">
          <a href="#"><i class="fa-brands fa-facebook fa-2x text-white"></i></a>
          <a href="#"><i class="fa-brands fa-instagram fa-2x text-white"></i></a>
          <a href="#"><i class="fa-brands fa-twitter fa-2x text-white"></i></a>
        </div>
      </div>

      <!-- About Section -->
      <div class="col-md-6 col-lg-3 px-md-4 mb-5 mb-lg-0">
        <div class="footer__list-title fw-bolder mb-1">عن متجرنا</div>
        <div class="footer__list list-unstyled p-0">
          <li><a class="footer__link text-decoration-none d-inline-block text-gray py-1" href="about.html">من نحن</a></li>
          <li><a class="footer__link text-decoration-none d-inline-block text-gray py-1" href="contact.html">تواصل معنا</a></li>
          <li><a class="footer__link text-decoration-none d-inline-block text-gray py-1" href="privacy-policy.html">سياسة الخصوصية</a></li>
          <li><a class="footer__link text-decoration-none d-inline-block text-gray py-1" href="refund-policy.html">سياسة الاستبدال والاسترجاع</a></li>
          <li><a class="footer__link text-decoration-none d-inline-block text-gray py-1" href="track-order.html">تتبع الطلب</a></li>
        </div>
      </div>

      <!-- Categories or Featured Branches -->
      <div class="col-md-6 col-lg-3 px-md-4 mb-5 mb-lg-0">
  <div class="footer__list-title fw-bolder mb-1">
    فروعنا
  </div>
  <div class="footer__list">
    <div class="d-flex gap-3 mb-3">
      <div class="fs-5"><i class="fa-solid fa-location-dot"></i></div>
      <div class="text-gray">فرع القاهرة: شارع التحرير - الدقي، أمام مكتبة الجامعة.</div>
    </div>
    <div class="d-flex gap-3 mb-3">
      <div class="fs-5"><i class="fa-solid fa-location-dot"></i></div>
      <div class="text-gray">فرع الإسكندرية: شارع فؤاد - محطة الرمل - بجوار مكتبة الإسكندرية.</div>
    </div>
    <div class="d-flex gap-3">
      <div class="fs-5"><i class="fa-solid fa-location-dot"></i></div>
      <div class="text-gray">فرع المنصورة: شارع الجمهورية - بجوار جامعة المنصورة.</div>
    </div>
  </div>
</div>


      <!-- Help & Newsletter -->
      <div class="col-md-6 col-lg-3 mb-5 mb-lg-0">
        <div>
          <div class="footer__list-title fw-bolder mb-1">هل تحتاج إلى مساعدة؟</div>
          <div class="d-flex gap-3 mb-3">
            <div class="fs-5"><i class="fa-solid fa-envelope"></i></div>
            <div class="text-gray">support@bookstore.com</div>
          </div>
        </div>
        <div>
          <div class="footer__list-title fw-bolder mb-3">اشترك في النشرة البريدية</div>
          <form class="footer__form position-relative">
            <input class="footer__email-input w-100 bg-transparent border border-white py-2 px-3 rounded-2 text-white pe-5" placeholder="البريد الإلكتروني">
            <button class="footer__submit mx-3 position-absolute top-50 translate-middle-y end-0 bg-transparent border-0 text-white d-flex align-items-center">
              <i class="fa-solid fa-paper-plane"></i>
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer Bottom -->
  <div class="footer__bottom text-center p-3 section-container">
    جميع الحقوق محفوظة © BookStore {{ now()->year }}
  </div>
</footer>
