// تهيئة التخزين المحلي
function setupStorage() {
  if (!localStorage.getItem('appointments')) {
    localStorage.setItem('appointments', JSON.stringify([]));
  }
  if (!localStorage.getItem('requests')) {
    localStorage.setItem('requests', JSON.stringify([]));
  }
}

// عرض رسالة للمستخدم
function showMessage(msg, type = 'success') {
  let messageBox = document.getElementById('messageBox');
  if (!messageBox) {
    messageBox = document.createElement('div');
    messageBox.id = 'messageBox';
    document.body.appendChild(messageBox);
  }
  
  messageBox.textContent = msg;
  messageBox.className = `message ${type}`;
  messageBox.style.display = 'block';
  
  // تأثير حركي للرسالة
  messageBox.style.animation = 'slideDown 0.3s forwards';
  
  setTimeout(() => {
    messageBox.style.animation = 'slideUp 0.3s forwards';
    setTimeout(() => {
      messageBox.style.display = 'none';
    }, 300);
  }, 3000);
}

// === وظائف الطبيب ===

// إضافة ميعاد جديد
function addAppointment() {
  const name = document.getElementById('doctorName').value;
  const specialty = document.getElementById('doctorSpecialty').value;
  const date = document.getElementById('appointmentDate').value;
  const time = document.getElementById('appointmentTime').value;
  const duration = document.getElementById('appointmentDuration').value;
  const link = document.getElementById('meetingLink').value.trim();

  if (!name || !specialty || !date || !time || !duration) {
    showMessage('يرجى ملء جميع الحقول المطلوبة', 'error');
    return;
  }

  // تنسيق التاريخ
  const dateObj = new Date(date);
  const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
  const formattedDate = dateObj.toLocaleDateString('ar-EG', options);

  const appointment = {
    id: Date.now(),
    name,
    specialty,
    date: formattedDate,
    time,
    duration: `${duration} دقيقة`,
    link,
    status: 'متاح'
  };

  const appointments = JSON.parse(localStorage.getItem('appointments')) || [];
  appointments.push(appointment);
  localStorage.setItem('appointments', JSON.stringify(appointments));

  showMessage('✅ تم إضافة الميعاد بنجاح!');

  // إعادة تعيين الحقول
  document.getElementById('appointmentDate').value = '';
  document.getElementById('appointmentTime').value = '';
  document.getElementById('meetingLink').value = '';
}

// عرض طلبات الحجز المعلقة
function loadPendingRequests() {
  const requests = JSON.parse(localStorage.getItem('requests')) || [];
  
  const requestsList = document.getElementById('pendingRequestsList');
  if (!requestsList) return;
  
  requestsList.innerHTML = '';
  
  // عرض الطلبات المنتظرة فقط
  const pendingRequests = requests.filter(r => r.status === 'بانتظار');
  
  if (pendingRequests.length === 0) {
    requestsList.innerHTML = '<p class="no-data">لا توجد طلبات حجز معلقة حالياً.</p>';
    return;
  }
  
  // عرض الطلبات المنتظرة
  pendingRequests.forEach(req => {
    const card = document.createElement('div');
    card.className = 'request-card pending';
    card.innerHTML = `
      <div class="request-header">
        <span class="patient-name">${req.patientName}</span>
        <span class="request-date">تم الطلب: ${req.requestDate}</span>
        <span class="status-label">${req.status}</span>
      </div>
      <div class="request-details">
        <p><strong>سبب الزيارة:</strong> ${req.reason}</p>
      </div>
      <div class="actions">
        <button class="accept" onclick="updateStatus(${req.id}, 'مقبول')">قبول</button>
        <button class="reject" onclick="updateStatus(${req.id}, 'مرفوض')">رفض</button>
      </div>
    `;
    requestsList.appendChild(card);
  });
}

function loadAcceptedRequests() {
  const requests = JSON.parse(localStorage.getItem('requests')) || [];

  const acceptedContainer = document.getElementById('acceptedRequestsList');
  if (!acceptedContainer) return;

  acceptedContainer.innerHTML = '';

  const acceptedRequests = requests.filter(req => req.status === 'مقبول');

  if (acceptedRequests.length === 0) {
    acceptedContainer.innerHTML = '<p class="no-data">لا توجد جلسات مؤكدة حالياً.</p>';
    return;
  }

  acceptedRequests.forEach(req => {
    const card = document.createElement('div');
    card.className = 'request-card accepted';

    card.innerHTML = `
      <div class="request-header">
        <span class="patient-name">${req.patientName}</span>
        <span class="status-label">مقبول</span>
      </div>
      <div class="request-details">
        <p><strong>سبب الجلسه:</strong> ${req.reason}</p>
        <p><strong>تاريخ الجلسه:</strong> ${req.requestDate}</p>
        <p><strong> وقت الجلسه:</strong> ${req.time}</p>

      </div>
      ${req.link ? `
      <div class="meeting-link">
        <a href="${req.link}" target="_blank">
          <button class="join-meeting-btn">انضم إلى الجلسة</button>
        </a>
      </div>
      ` : ''}
      <div class="actions">
        <button class="cancel" onclick="updateStatus(${req.id}, 'ملغي')">إلغاء الجلسة</button>
      </div>
    `;

    acceptedContainer.appendChild(card);
  });
}

// تحديث حالة الطلب
function updateStatus(requestId, newStatus) {
  const requests = JSON.parse(localStorage.getItem('requests')) || [];
  
  const requestIndex = requests.findIndex(req => req.id === requestId);
  if (requestIndex === -1) {
    showMessage('الطلب غير موجود', 'error');
    return;
  }
  
  if (newStatus === 'مقبول') {
    // قبول الطلب وإضافته للجلسات المؤكدة
    requests[requestIndex].status = 'مقبول';
    localStorage.setItem('requests', JSON.stringify(requests));
    showMessage('✅ تم قبول الطلب بنجاح!');
  } else if (newStatus === 'مرفوض' || newStatus === 'ملغي') {
    // حذف الطلب المرفوض أو الملغي نهائياً
    requests.splice(requestIndex, 1);
    localStorage.setItem('requests', JSON.stringify(requests));
    showMessage(`✅ تم ${newStatus === 'مرفوض' ? 'رفض' : 'إلغاء'} الطلب بنجاح!`);
  }
  
  // تحديث القوائم
  if (document.getElementById('pendingRequestsList')) {
    loadPendingRequests();
  }
  if (document.getElementById('acceptedRequestsList')) {
    loadAcceptedRequests();
  }
  if (document.getElementById('myAppointmentsList')) {
    loadMyAppointments();
  }
}

// === وظائف المريض ===

// تحديث قائمة التخصصات في الفلتر
function updateSpecialtyFilter(newSpecialty) {
  const filterSelect = document.getElementById('filterSpecialty');
  if (!filterSelect) return;
  
  // التحقق من وجود التخصص في القائمة
  let exists = false;
  for (let i = 0; i < filterSelect.options.length; i++) {
    if (filterSelect.options[i].value === newSpecialty) {
      exists = true;
      break;
    }
  }
  
  // إضافة التخصص إذا لم يكن موجودًا
  if (!exists && newSpecialty) {
    const option = document.createElement('option');
    option.value = newSpecialty;
    option.textContent = newSpecialty;
    filterSelect.appendChild(option);
  }
}

// ملء قائمة التخصصات في الفلتر
function populateSpecialtyFilter() {
  const filterSelect = document.getElementById('filterSpecialty');
  if (!filterSelect) return;
  
  const appointments = JSON.parse(localStorage.getItem('appointments')) || [];
  const specialties = new Set();
  
  // جمع جميع التخصصات الفريدة
  appointments.forEach(app => {
    if (app.specialty) {
      specialties.add(app.specialty);
    }
  });
  
  // إفراغ القائمة ماعدا أول عنصر "الكل"
  while (filterSelect.options.length > 1) {
    filterSelect.remove(1);
  }
  
  // إضافة التخصصات
  specialties.forEach(specialty => {
    const option = document.createElement('option');
    option.value = specialty;
    option.textContent = specialty;
    filterSelect.appendChild(option);
  });
}

// عرض المواعيد المتاحة
function loadAppointments() {
  const searchTerm = document.getElementById('searchInput')?.value?.toLowerCase() || '';
  const filterSpecialty = document.getElementById('filterSpecialty')?.value || '';
  
  let appointments = JSON.parse(localStorage.getItem('appointments')) || [];
  const list = document.getElementById('appointmentsList');
  if (!list) return;
  
  list.innerHTML = '';

  // تطبيق الفلاتر - عرض المواعيد المتاحة فقط
  appointments = appointments.filter(app => {
    const matchesSearch = !searchTerm || 
                          app.name.toLowerCase().includes(searchTerm) || 
                          app.specialty.toLowerCase().includes(searchTerm);
    const matchesSpecialty = !filterSpecialty || app.specialty === filterSpecialty;
    return matchesSearch && matchesSpecialty && app.status === 'متاح';
  });

  if (appointments.length === 0) {
    list.innerHTML = '<p class="no-data">لا توجد مواعيد متاحة حالياً.</p>';
    return;
  }

  // عرض المواعيد بتنسيق بطاقات
  appointments.forEach(app => {
    const div = document.createElement('div');
    div.className = 'appointment-card';
    div.innerHTML = `
      <div class="doctor-info">
        <span class="doctor-name">${app.name}</span>
        <span class="specialty">${app.specialty}</span>
        <span class="appointment-time">${app.date} - ${app.time} (${app.duration})</span>
      </div>
      <button class="book-btn" onclick="requestAppointment(${app.id})">حجز الميعاد</button>
    `;
    list.appendChild(div);
  });
  
  // تحديث قائمة التخصصات في الفلتر
  populateSpecialtyFilter();
}

// عرض المواعيد المحجوزة للمريض
function loadMyAppointments() {
  const requests = JSON.parse(localStorage.getItem('requests')) || [];
  
  const list = document.getElementById('myAppointmentsList');
  if (!list) return;
  
  list.innerHTML = '';

  if (requests.length === 0) {
    list.innerHTML = '<p class="no-data">لا توجد مواعيد محجوزة حالياً.</p>';
    return;
  }

  const sortedRequests = [...requests].sort((a, b) => b.id - a.id);
  const latestRequests = sortedRequests.slice(0, 8);

  latestRequests.forEach(req => {
    const card = document.createElement('div');
    card.className = `request-card ${req.status === 'بانتظار' ? 'pending' : 'accepted'}`;
    
    card.innerHTML = `
      <div class="request-header">
        <span class="status-label">${req.status === 'بانتظار' ? 'بانتظار الموافقة' : 'مؤكد'}</span>
      </div>
      <div class="request-details">
        <p><strong>اسم المريض:</strong> ${req.patientName}</p>
        <p><strong>تاريخ الجلسه:</strong> ${req.requestDate}</p>
        <p><strong> وقت الجلسه:</strong> ${req.time}</p>
      </div>
      ${req.status !== 'بانتظار' && req.link ? `
        <div class="meeting-link">
          <a href="${req.link}" target="_blank">
            <button class="join-meeting-btn">انضم إلى الجلسة</button>
          </a>
        </div>
      ` : ''}
      <div class="actions">
        <button class="delete" onclick="deleteSession(${req.id})">🗑️ الغاء الجلسة</button>
      </div>
    `;
    
    list.appendChild(card);
  });
}

function deleteSession(requestId) {
  if (!confirm('⚠️ هل أنت متأكد أنك تريد حذف هذه الجلسة نهائياً؟')) return;

  const requests = JSON.parse(localStorage.getItem('requests')) || [];
  const updatedRequests = requests.filter(req => req.id !== requestId);

  localStorage.setItem('requests', JSON.stringify(updatedRequests));
  
  showMessage('✅ تم حذف الجلسة بنجاح!');
  
  // تحديث القوائم
  if (document.getElementById('acceptedRequestsList')) {
    loadAcceptedRequests();
  }
  if (document.getElementById('pendingRequestsList')) {
    loadPendingRequests();
  }
  if (document.getElementById('myAppointmentsList')) {
    loadMyAppointments();
  }
}


// طلب حجز ميعاد
function requestAppointment(appointmentId) {
  const appointments = JSON.parse(localStorage.getItem('appointments')) || [];
  const appointmentIndex = appointments.findIndex(a => a.id === appointmentId);
  
  if (appointmentIndex === -1 || appointments[appointmentIndex].status !== 'متاح') {
    showMessage('هذا الميعاد غير متاح', 'error');
    return;
  }
  
  const appointment = appointments[appointmentIndex];
  
  const patientName = prompt('الرجاء إدخال اسمك:');
  if (!patientName) {
    showMessage('يجب إدخال اسم المريض', 'error');
    return;
  }
  
  const reason = prompt('سبب الزيارة (اختياري):') || 'غير محدد';
  
  const requests = JSON.parse(localStorage.getItem('requests')) || [];
  
const newRequest = {
  id: Date.now(),
  appointmentId,
  patientName,
  reason,
  requestDate: new Date().toLocaleDateString('ar-EG'),
  doctorName: appointment.name,
  specialty: appointment.specialty,
  date: appointment.date,
  time: appointment.time,
  duration: appointment.duration,
  link: appointment.link, // ✅ أضف الرابط هنا
  status: 'بانتظار'
};

  
  requests.push(newRequest);
  localStorage.setItem('requests', JSON.stringify(requests));
  
  // إزالة الميعاد من القائمة المتاحة فوراً
  appointments.splice(appointmentIndex, 1);
  localStorage.setItem('appointments', JSON.stringify(appointments));
  
  showMessage('✅ تم إرسال طلب الحجز بنجاح!');
  
  // تحديث القوائم
  loadAppointments();
  loadMyAppointments();
}

// إعداد الصفحة عند التحميل (مشترك)
document.addEventListener('DOMContentLoaded', function() {
  // تهيئة التخزين المحلي
  setupStorage();
  
  // ربط وظائف البحث إذا كانت متاحة
  const searchInput = document.getElementById('searchInput');
  if (searchInput) {
    searchInput.addEventListener('input', loadAppointments);
  }
  
  const filterSpecialty = document.getElementById('filterSpecialty');
  if (filterSpecialty) {
    filterSpecialty.addEventListener('change', loadAppointments);
  }
  
  // تحميل البيانات المناسبة حسب العناصر المتاحة في الصفحة
  if (document.getElementById('appointmentsList')) {
    loadAppointments();
  }
  
  if (document.getElementById('myAppointmentsList')) {
    loadMyAppointments();
  }
  
  if (document.getElementById('pendingRequestsList')) {
    loadPendingRequests();
  }
  
  if (document.getElementById('acceptedRequestsList')) {
    loadAcceptedRequests();
  }
});