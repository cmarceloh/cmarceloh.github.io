/* ═══════════════════════════════════════════════
   script.js — Generado por Landing Page Builder
   ════════════════════════════════════════════════ */

(function () {
  "use strict";

  // ── Modo Claro / Modo Oscuro Toggle ──
  function initThemeToggle() {
    var currentTheme =
      document.documentElement.getAttribute("data-theme") ||
      localStorage.getItem("cmh-site-theme") ||
      "dark";
    document.documentElement.setAttribute("data-theme", currentTheme);

    var toggles = document.querySelectorAll(".theme-toggle-btn");
    toggles.forEach(function (btn) {
      btn.addEventListener("click", function (e) {
        e.preventDefault();
        var cur = document.documentElement.getAttribute("data-theme") || "dark";
        var next = cur === "dark" ? "light" : "dark";
        document.documentElement.setAttribute("data-theme", next);
        try {
          localStorage.setItem("cmh-site-theme", next);
        } catch (err) {}
      });
    });
  }
  initThemeToggle();

  // ── Menú Hamburguesa Responsive ──
  var hamburgerBtn = document.getElementById("siteHamburger");
  var mobileDrawer = document.getElementById("siteMobileDrawer");
  var mobileOverlay = document.getElementById("siteMobileOverlay");
  var mobileCloseBtn = document.getElementById("siteMobileClose");

  function openMobileMenu() {
    if (!hamburgerBtn || !mobileDrawer || !mobileOverlay) return;
    hamburgerBtn.classList.add("is-active");
    hamburgerBtn.setAttribute("aria-expanded", "true");
    mobileDrawer.classList.add("is-open");
    mobileOverlay.classList.add("is-open");
    document.body.style.overflow = "hidden";
  }

  function closeMobileMenu() {
    if (!hamburgerBtn || !mobileDrawer || !mobileOverlay) return;
    hamburgerBtn.classList.remove("is-active");
    hamburgerBtn.setAttribute("aria-expanded", "false");
    mobileDrawer.classList.remove("is-open");
    mobileOverlay.classList.remove("is-open");
    document.body.style.overflow = "";
  }

  if (hamburgerBtn) {
    hamburgerBtn.addEventListener("click", function () {
      var isOpen = mobileDrawer && mobileDrawer.classList.contains("is-open");
      if (isOpen) {
        closeMobileMenu();
      } else {
        openMobileMenu();
      }
    });
  }

  if (mobileOverlay) mobileOverlay.addEventListener("click", closeMobileMenu);
  if (mobileCloseBtn) mobileCloseBtn.addEventListener("click", closeMobileMenu);

  // Cerrar menú al tocar un link
  document.querySelectorAll(".mobile-nav-links a").forEach(function (link) {
    link.addEventListener("click", closeMobileMenu);
  });

  // ── Formulario de contacto (Fetch API → FormSubmit) ──
  var form = document.getElementById("contact-form");
  if (form) {
    form.addEventListener("submit", function (e) {
      e.preventDefault();

      var btn = form.querySelector('button[type="submit"]');
      var msgEl = document.getElementById("contact-msg");
      var originalText = btn ? btn.textContent : "";

      if (btn) {
        btn.textContent = "Enviando...";
        btn.disabled = true;
      }
      if (msgEl) {
        msgEl.className = "contact-msg";
        msgEl.textContent = "";
      }

      var formData = new FormData(form);
      var data = {};
      formData.forEach(function (value, key) {
        data[key] = value;
      });

      fetch(form.action, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
        },
        body: JSON.stringify(data),
      })
        .then(function (res) {
          return res.json();
        })
        .then(function (json) {
          if (json.success === true || json.success === "true") {
            if (msgEl) {
              msgEl.className = "contact-msg success";
              msgEl.textContent =
                "¡Gracias! Tu mensaje fue enviado correctamente.";
            }
            form.reset();
          } else {
            throw new Error(json.message || "No se pudo enviar el mensaje.");
          }
        })
        .catch(function (err) {
          console.error("Error formulario:", err);
          if (msgEl) {
            msgEl.className = "contact-msg error";
            msgEl.textContent =
              err.message || "Error al enviar. Por favor, intenta nuevamente.";
          }
        })
        .finally(function () {
          if (btn) {
            btn.textContent = originalText;
            btn.disabled = false;
          }
        });
    });
  }

  // ── Smooth scroll para links internos con compensación del header ──
  document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
    anchor.addEventListener("click", function (e) {
      var targetId = this.getAttribute("href").substring(1);
      var target = document.getElementById(targetId);
      if (target) {
        e.preventDefault();
        var headerOffset = 72;
        var elementPosition = target.getBoundingClientRect().top;
        var offsetPosition =
          elementPosition + window.pageYOffset - headerOffset;
        window.scrollTo({
          top: offsetPosition,
          behavior: "smooth",
        });
      }
    });
  });

  // ── Modal Emergente de Imagen (Lightbox Centrado) ──
  window.openImageModal = function (src, title, desc) {
    var modal = document.getElementById("imageDetailModal");
    if (!modal) return;
    var imgEl = document.getElementById("imgModalSrc");
    var titleEl = document.getElementById("imgModalTitle");
    var descEl = document.getElementById("imgModalDesc");

    if (imgEl) imgEl.src = src || "";
    if (titleEl) {
      titleEl.textContent = title || "";
      titleEl.style.display = title ? "block" : "none";
    }
    if (descEl) {
      descEl.textContent = desc || "";
      descEl.style.display = desc ? "block" : "none";
    }

    modal.style.display = "flex";
    requestAnimationFrame(function () {
      modal.classList.add("is-open");
    });
    document.body.style.overflow = "hidden";
  };

  window.closeImageModal = function () {
    var modal = document.getElementById("imageDetailModal");
    if (!modal) return;
    modal.classList.remove("is-open");
    document.body.style.overflow = "";
    setTimeout(function () {
      modal.style.display = "none";
    }, 300);
  };

  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
      window.closeImageModal();
    }
  });

  // ── Slideshow Continuo Horizontal (deslizamiento secuencial de izquierda a derecha) ──
  var dualSliders = document.querySelectorAll(".dual-slider-container");
  dualSliders.forEach(function (slider) {
    var stage = slider.querySelector(".dual-slider-stage");
    var cards = slider.querySelectorAll(".dual-slide-card");
    var prevBtn = slider.querySelector(".dual-slider-prev");
    var nextBtn = slider.querySelector(".dual-slider-next");
    var dots = slider.querySelectorAll(".dual-slider-dot");
    var progressBar = slider.querySelector(".slider-progress-fill");
    var intervalSec = parseInt(
      slider.getAttribute("data-interval") || "10",
      10,
    );
    var intervalMs = intervalSec * 1000;

    if (!stage || cards.length === 0) return;

    var currentIndex = 0;
    var timer = null;

    function updateDots(activeIdx) {
      dots.forEach(function (dot, idx) {
        if (idx === activeIdx) {
          dot.classList.add("is-active");
        } else {
          dot.classList.remove("is-active");
        }
      });
    }

    function updateSliderPosition(animate) {
      var totalCards = cards.length;
      if (totalCards === 0) return;

      var isMobile = window.innerWidth <= 600;
      var isTablet = window.innerWidth > 600 && window.innerWidth <= 992;
      var maxIndex = isMobile ? totalCards - 1 : Math.max(0, totalCards - 2);

      if (currentIndex > maxIndex) {
        currentIndex = 0;
      } else if (currentIndex < 0) {
        currentIndex = maxIndex;
      }

      var firstCard = cards[0];
      if (!firstCard) return;

      var cardWidth =
        firstCard.getBoundingClientRect().width ||
        firstCard.offsetWidth ||
        (stage.parentElement
          ? stage.parentElement.offsetWidth / (isMobile ? 1 : 2) - 12
          : 320);
      var gap = isMobile || isTablet ? 16 : 24;
      var offset = currentIndex * (cardWidth + gap);

      if (animate === false) {
        stage.style.transition = "none";
      } else {
        stage.style.transition =
          "transform 0.65s cubic-bezier(0.25, 1, 0.5, 1)";
      }

      stage.style.transform = "translateX(-" + offset + "px)";
      updateDots(currentIndex);
    }

    function startProgress() {
      if (!progressBar) return;
      progressBar.style.transition = "none";
      progressBar.style.width = "0%";
      requestAnimationFrame(function () {
        setTimeout(function () {
          progressBar.style.transition = "width " + intervalSec + "s linear";
          progressBar.style.width = "100%";
        }, 30);
      });
    }

    function nextSlide() {
      currentIndex++;
      updateSliderPosition(true);
      startProgress();
    }

    function prevSlide() {
      currentIndex--;
      updateSliderPosition(true);
      startProgress();
    }

    function resetAutoplay() {
      if (timer) clearInterval(timer);
      startProgress();
      timer = setInterval(nextSlide, intervalMs);
    }

    if (nextBtn) {
      nextBtn.addEventListener("click", function (e) {
        e.preventDefault();
        e.stopPropagation();
        nextSlide();
        resetAutoplay();
      });
    }

    if (prevBtn) {
      prevBtn.addEventListener("click", function (e) {
        e.preventDefault();
        e.stopPropagation();
        prevSlide();
        resetAutoplay();
      });
    }

    dots.forEach(function (dot) {
      dot.addEventListener("click", function (e) {
        e.preventDefault();
        e.stopPropagation();
        var targetIdx = parseInt(
          this.getAttribute("data-dot-index") || "0",
          10,
        );
        currentIndex = targetIdx;
        updateSliderPosition(true);
        resetAutoplay();
      });
    });

    window.addEventListener("resize", function () {
      updateSliderPosition(false);
    });

    // Inicialización
    updateSliderPosition(false);
    resetAutoplay();
  });

  // ── Animación de entrada de elementos al hacer scroll ──
  if ("IntersectionObserver" in window) {
    var observer = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            entry.target.style.opacity = "1";
            entry.target.style.transform = "translateY(0)";
            observer.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.1 },
    );

    document
      .querySelectorAll(
        ".feature-card, .testimonial-card, .gallery-card, .about-img-box, .dual-slide-card, .dual-slider-box",
      )
      .forEach(function (el) {
        el.style.opacity = "0";
        el.style.transform = "translateY(24px)";
        el.style.transition =
          "opacity .6s cubic-bezier(0.16, 1, 0.3, 1), transform .6s cubic-bezier(0.16, 1, 0.3, 1)";
        observer.observe(el);
      });
  }
})();
