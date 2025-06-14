document.addEventListener("DOMContentLoaded", () => {
  const userName = localStorage.getItem("user_name") || "مستخدم";
  const nameElement = document.getElementById("userName");
  if (nameElement) nameElement.innerText = userName;

  // تفعيل Dark Mode إذا موجود
  if (localStorage.getItem("dark_mode") === "true") {
      document.body.classList.add("dark-mode");
  }

  // جلب نموذج تعديل الملف الشخصي
  const profileForm = document.getElementById('profileForm');
  if (profileForm) {
      profileForm.addEventListener('submit', function(event) {
          event.preventDefault(); // منع الإرسال التقليدي للنموذج

          const formData = new FormData(profileForm);

          fetch('update_profile.php', {
              method: 'POST',
              body: formData
          })
          .then(response => response.json())
          .then(data => {
              if (data.status === 'success') {
                  Swal.fire('تم الحفظ!', data.message, 'success'); // استخدام SweetAlert لعرض رسالة النجاح
                  // يمكنك هنا إضافة أي إجراء آخر بعد النجاح مثل تحديث الواجهة
              } else {
                  Swal.fire('خطأ!', data.message, 'error'); // استخدام SweetAlert لعرض رسالة الخطأ
              }
          })
          .catch(error => {
              console.error('حدث خطأ أثناء إرسال البيانات:', error);
              Swal.fire('خطأ!', 'حدث خطأ غير متوقع أثناء حفظ البيانات.', 'error');
          });
      });
  }

  // دالة لجلب بيانات المستخدم لملء النموذج (تحتاج إلى تنفيذ ملف PHP لجلب البيانات)
  function fetchUserData() {
      fetch('get_user_data.php') // اسم الملف الذي سيجلب بيانات المستخدم
          .then(response => response.json())
          .then(data => {
              if (data.status === 'success' && data.user) {
                  document.getElementById('email').value = data.user.email || '';
                  document.getElementById('phone').value = data.user.phone || '';
                  document.getElementById('birthdate').value = data.user.birthdate || '';
                  document.getElementById('national_id').value = data.user.national_id || '';
                  document.getElementById('blood_type').value = data.user.blood_type || '';
                  document.getElementById('job').value = data.user.job || '';
                  document.getElementById('marital_status').value = data.user.marital_status || 'أعزب'; // قيمة افتراضية
                  document.getElementById('city').value = data.user.city || '';
                  document.getElementById('address').value = data.user.address || '';
                  document.getElementById('gender').value = data.user.gender || 'ذكر'; // قيمة افتراضية
              } else {
                  console.error('فشل في جلب بيانات المستخدم:', data.message);
                  Swal.fire('خطأ!', 'فشل في تحميل بيانات المستخدم.', 'error');
              }
          })
          .catch(error => {
              console.error('حدث خطأ أثناء جلب بيانات المستخدم:', error);
              Swal.fire('خطأ!', 'حدث خطأ غير متوقع أثناء تحميل البيانات.', 'error');
          });
  }

  // استدعاء دالة جلب البيانات عند تحميل الصفحة (إذا كنت تريد ملء النموذج ببيانات موجودة)
  fetchUserData();
});

// زر تغيير الوضع الليلي
function toggleDarkMode() {
  document.body.classList.toggle("dark-mode");
  localStorage.setItem("dark_mode", document.body.classList.contains("dark-mode"));
}

// تسجيل الخروج
function logout() {
  localStorage.clear();
  window.location.href = "index.html";
}

// فتح الشات
function openChat() {
  Swal.fire('سيتم توجيهك للطبيب الآن!', '', 'info');
}

// الرجوع للخلف
function goBack() {
  window.history.back();
}