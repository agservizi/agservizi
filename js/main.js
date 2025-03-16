document.addEventListener("DOMContentLoaded", () => {
  // Initialize AOS (Animate On Scroll)
  AOS.init({
    duration: 800,
    easing: "ease-in-out",
    once: true,
    mirror: false,
  })

  // Mobile Menu Toggle
  const mobileMenuToggle = document.querySelector(".mobile-menu-toggle")
  const navList = document.querySelector(".nav-list")

  if (mobileMenuToggle) {
    mobileMenuToggle.addEventListener("click", function () {
      this.classList.toggle("active")
      navList.classList.toggle("active")

      // Toggle hamburger menu animation
      const bars = this.querySelectorAll(".bar")
      if (this.classList.contains("active")) {
        bars[0].style.transform = "rotate(-45deg) translate(-5px, 6px)"
        bars[1].style.opacity = "0"
        bars[2].style.transform = "rotate(45deg) translate(-5px, -6px)"
      } else {
        bars[0].style.transform = "none"
        bars[1].style.opacity = "1"
        bars[2].style.transform = "none"
      }
    })
  }

  // Mobile Dropdown Toggle
  const dropdowns = document.querySelectorAll(".dropdown")

  dropdowns.forEach((dropdown) => {
    const link = dropdown.querySelector("a")

    if (window.innerWidth < 768) {
      link.addEventListener("click", (e) => {
        e.preventDefault()
        dropdown.classList.toggle("active")
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

  // Partners Slider (Simple Auto Scroll)
  const partnersSlider = document.querySelector(".partners-slider")

  if (partnersSlider) {
    let isDown = false
    let startX
    let scrollLeft

    partnersSlider.addEventListener("mousedown", (e) => {
      isDown = true
      startX = e.pageX - partnersSlider.offsetLeft
      scrollLeft = partnersSlider.scrollLeft
    })

    partnersSlider.addEventListener("mouseleave", () => {
      isDown = false
    })

    partnersSlider.addEventListener("mouseup", () => {
      isDown = false
    })

    partnersSlider.addEventListener("mousemove", (e) => {
      if (!isDown) return
      e.preventDefault()
      const x = e.pageX - partnersSlider.offsetLeft
      const walk = (x - startX) * 2
      partnersSlider.scrollLeft = scrollLeft - walk
    })
  }

  // Form Validation
  const forms = document.querySelectorAll("form")

  forms.forEach((form) => {
    form.addEventListener("submit", (e) => {
      let isValid = true
      const requiredFields = form.querySelectorAll("[required]")

      requiredFields.forEach((field) => {
        if (!field.value.trim()) {
          isValid = false
          field.classList.add("error")

          // Create error message if it doesn't exist
          let errorMessage = field.nextElementSibling
          if (!errorMessage || !errorMessage.classList.contains("error-message")) {
            errorMessage = document.createElement("div")
            errorMessage.classList.add("error-message")
            errorMessage.textContent = "Questo campo è obbligatorio"
            field.parentNode.insertBefore(errorMessage, field.nextSibling)
          }
        } else {
          field.classList.remove("error")

          // Remove error message if it exists
          const errorMessage = field.nextElementSibling
          if (errorMessage && errorMessage.classList.contains("error-message")) {
            errorMessage.remove()
          }

          // Email validation
          if (field.type === "email" && !validateEmail(field.value)) {
            isValid = false
            field.classList.add("error")

            let errorMessage = field.nextElementSibling
            if (!errorMessage || !errorMessage.classList.contains("error-message")) {
              errorMessage = document.createElement("div")
              errorMessage.classList.add("error-message")
              errorMessage.textContent = "Inserisci un indirizzo email valido"
              field.parentNode.insertBefore(errorMessage, field.nextSibling)
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
  function validateEmail(email) {
    const re =
      /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
    return re.test(String(email).toLowerCase())
  }
})

