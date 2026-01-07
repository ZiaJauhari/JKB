// Form submission for consultation (existing form in contact section)
const consultationForm = document.getElementById("consultationForm");
if (consultationForm) {
  consultationForm.addEventListener("submit", function (e) {
    e.preventDefault();
    alert(
      "Terima kasih! Pesan Anda telah dikirim. Kami akan segera menghubungi Anda."
    );
    // Untuk integrasi email, ganti dengan fetch ke API seperti Formspree.
  });
}

// Form submission for free consultation modal (new feature)
const freeConsultationForm = document.getElementById("freeConsultationForm");
if (freeConsultationForm) {
  freeConsultationForm.addEventListener("submit", function (e) {
    e.preventDefault();

    // Get form values
    const fullName = document.getElementById("fullName").value.trim();
    const companyName = document.getElementById("companyName").value.trim();
    const phoneNumber = document.getElementById("phoneNumber").value.trim();
    const emailAddress = document.getElementById("emailAddress").value.trim();

    // Validate form fields
    if (!fullName || !companyName || !phoneNumber || !emailAddress) {
      alert("Mohon lengkapi semua field yang diperlukan!");
      return;
    }

    // Format WhatsApp message
    const message =
      `Halo, saya ingin ekspor barang.%0A%0A` +
      `Nama: ${encodeURIComponent(fullName)}%0A` +
      `Perusahaan/Bisnis: ${encodeURIComponent(companyName)}%0A` +
      `No. Telepon: ${encodeURIComponent(phoneNumber)}%0A` +
      `Email: ${encodeURIComponent(emailAddress)}%0A%0A` +
      `Mohon informasi lebih lanjut mengenai layanan ekspor barang.`;

    // WhatsApp number (replace with actual number)
    const whatsappNumber = "6285754828055";

    // Create WhatsApp URL
    const whatsappURL = `https://wa.me/${whatsappNumber}?text=${message}`;

    // Close modal
    const modal = bootstrap.Modal.getInstance(
      document.getElementById("consultationModal")
    );
    if (modal) {
      modal.hide();
    }

    // Open WhatsApp in new tab
    window.open(whatsappURL, "_blank");

    // Reset form
    freeConsultationForm.reset();
  });
}

// Form submission for newsletter
const newsletterForm = document.getElementById("newsletterForm");
if (newsletterForm) {
  newsletterForm.addEventListener("submit", function (e) {
    e.preventDefault();
    alert("Terima kasih telah subscribe! Kami akan mengirim update terbaru.");
  });
}

// Counter animation function
function animateCounters() {
  const counters = document.querySelectorAll(".counter");
  counters.forEach((counter) => {
    const target = +counter.getAttribute("data-target");
    const increment = target / 100; // Sesuaikan kecepatan animasi
    let current = 0;
    const timer = setInterval(() => {
      current += increment;
      if (current >= target) {
        current = target;
        clearInterval(timer);
      }
      counter.textContent = Math.floor(current);
    }, 20); // Interval 20ms untuk animasi smooth
  });
}

// Trigger counter animation when stats section is visible
const statsSection = document.querySelector(".stats");
const observer = new IntersectionObserver(
  (entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        animateCounters();
        observer.unobserve(entry.target); // Hentikan observasi setelah animasi
      }
    });
  },
  { threshold: 0.5 }
);
if (statsSection) {
  observer.observe(statsSection);
}

// Fade-in animation for sections on scroll
const sections = document.querySelectorAll(".fade-in");
const fadeObserver = new IntersectionObserver(
  (entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add("visible");
      }
    });
  },
  { threshold: 0.1 }
);
sections.forEach((section) => {
  fadeObserver.observe(section);
});

// Smooth scroll for navigation links
document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
  anchor.addEventListener("click", function (e) {
    e.preventDefault();
    const target = document.querySelector(this.getAttribute("href"));
    if (target) {
      target.scrollIntoView({
        behavior: "smooth",
      });
    }
  });
});
