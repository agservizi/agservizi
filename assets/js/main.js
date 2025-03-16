/**
 * AG Servizi Via Plinio 72
 * Main JavaScript File
 */

document.addEventListener("DOMContentLoaded", () => {
  // Initialize AOS (Animate On Scroll)
  if (typeof AOS !== "undefined") {
    AOS.init({
      duration: 800,
      easing: "ease",
      once: true,
      offset: 100,
    })
  }

  // Mobile Menu Toggle
  const mobileMenuToggle = document.querySelector(".mobile-menu-toggle")
  const mobileMenu = document.querySelector(".mobile-menu")

  if (mobileMenuToggle) {
    mobileMenuToggle.addEventListener("click", function () {
      this.classList.toggle("active")
      mobileMenu.classList.toggle("active")
      document.body.classList.toggle("no-scroll")
    })
  }

  // Mobile Dropdown Toggle
  const mobileNavItems = document.querySelectorAll(".mobile-nav-list > li")

  mobileNavItems.forEach((item) => {
    const hasDropdown = item.querySelector(".mobile-dropdown")

    if (hasDropdown) {
      const link = item.querySelector("a")

      link.addEventListener("click", function (e) {
        e.preventDefault()
        this.classList.toggle("active")
        hasDropdown.classList.toggle("active")
      })
    }
  })

  // Header Scroll Effect
  const header = document.querySelector(".header")

  window.addEventListener("scroll", () => {
    if (window.scrollY > 50) {
      header.classList.add("scrolled")
    } else {
      header.classList.remove("scrolled")
    }
  })

  // Back to Top Button
  const backToTopBtn = document.getElementById("backToTop")

  if (backToTopBtn) {
    window.addEventListener("scroll", () => {
      if (window.scrollY > 300) {
        backToTopBtn.classList.add("active")
      } else {
        backToTopBtn.classList.remove("active")
      }
    })

    backToTopBtn.addEventListener("click", (e) => {
      e.preventDefault()
      window.scrollTo({
        top: 0,
        behavior: "smooth",
      })
    })
  }

  // Form Validation
  const forms = document.querySelectorAll("form")

  forms.forEach((form) => {
    form.addEventListener("submit", (e) => {
      const requiredFields = form.querySelectorAll("[required]")
      let isValid = true

      requiredFields.forEach((field) => {
        if (!field.value.trim()) {
          isValid = false
          field.classList.add("error")

          // Create error message if it doesn't exist
          let errorMsg = field.nextElementSibling
          if (!errorMsg || !errorMsg.classList.contains("error-message")) {
            errorMsg = document.createElement("div")
            errorMsg.classList.add("error-message")
            errorMsg.textContent = "Questo campo è obbligatorio"
            field.parentNode.insertBefore(errorMsg, field.nextSibling)
          }
        } else {
          field.classList.remove("error")

          // Remove error message if it exists
          const errorMsg = field.nextElementSibling
          if (errorMsg && errorMsg.classList.contains("error-message")) {
            errorMsg.remove()
          }

          // Email validation
          if (field.type === "email" && !isValidEmail(field.value)) {
            isValid = false
            field.classList.add("error")

            let errorMsg = field.nextElementSibling
            if (!errorMsg || !errorMsg.classList.contains("error-message")) {
              errorMsg = document.createElement("div")
              errorMsg.classList.add("error-message")
              errorMsg.textContent = "Inserisci un indirizzo email valido"
              field.parentNode.insertBefore(errorMsg, field.nextSibling)
            }
          }
        }
      })

      if (!isValid) {
        e.preventDefault()
      }
    })
  })

  // Email validation function
  function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    return emailRegex.test(email)
  }

  // Remove error class on input
  const formInputs = document.querySelectorAll("input, textarea, select")

  formInputs.forEach((input) => {
    input.addEventListener("input", function () {
      this.classList.remove("error")

      // Remove error message if it exists
      const errorMsg = this.nextElementSibling
      if (errorMsg && errorMsg.classList.contains("error-message")) {
        errorMsg.remove()
      }
    })
  })
})

