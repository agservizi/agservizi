/**
 * AG Servizi Via Plinio 72
 * Admin Panel JavaScript
 */

document.addEventListener("DOMContentLoaded", () => {
  // Toggle mobile sidebar
  const sidebarToggle = document.querySelector(".sidebar-toggle")
  const adminSidebar = document.querySelector(".admin-sidebar")
  const adminMain = document.querySelector(".admin-main")

  if (sidebarToggle) {
    sidebarToggle.addEventListener("click", () => {
      adminSidebar.classList.toggle("collapsed")
      adminMain.classList.toggle("expanded")
    })
  }

  // Dropdown menus
  const dropdownToggles = document.querySelectorAll(".dropdown-toggle")

  dropdownToggles.forEach((toggle) => {
    toggle.addEventListener("click", function (e) {
      e.preventDefault()
      this.nextElementSibling.classList.toggle("show")
    })
  })

  // Close dropdowns when clicking outside
  document.addEventListener("click", (e) => {
    const dropdowns = document.querySelectorAll(".dropdown-menu")

    dropdowns.forEach((dropdown) => {
      if (!dropdown.contains(e.target) && !e.target.classList.contains("dropdown-toggle")) {
        dropdown.classList.remove("show")
      }
    })
  })

  // Notifications dropdown
  const notificationBtn = document.querySelector(".notification-btn")
  const notificationDropdown = document.querySelector(".notification-dropdown")

  if (notificationBtn && notificationDropdown) {
    notificationBtn.addEventListener("click", (e) => {
      e.stopPropagation()
      notificationDropdown.classList.toggle("show")
    })
  }

  // Form validation
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
        }
      })

      if (!isValid) {
        e.preventDefault()
      }
    })
  })

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

  // Animate stats numbers
  const statNumbers = document.querySelectorAll(".stat-number")

  if (statNumbers.length > 0) {
    statNumbers.forEach((stat) => {
      const target = Number.parseInt(stat.getAttribute("data-count"))
      let count = 0
      const duration = 2000 // 2 seconds
      const increment = target / (duration / 16) // 60fps

      const timer = setInterval(() => {
        count += increment

        if (count >= target) {
          clearInterval(timer)
          stat.textContent = target
        } else {
          stat.textContent = Math.floor(count)
        }
      }, 16)
    })
  }

  // Confirm delete actions
  const deleteButtons = document.querySelectorAll(".delete-btn")

  deleteButtons.forEach((button) => {
    button.addEventListener("click", (e) => {
      if (!confirm("Sei sicuro di voler eliminare questo elemento?")) {
        e.preventDefault()
      }
    })
  })
})

