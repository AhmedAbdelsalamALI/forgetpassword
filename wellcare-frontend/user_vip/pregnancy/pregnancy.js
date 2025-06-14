document.addEventListener('DOMContentLoaded', function() {
    // عناصر واجهة المستخدم
    const calculateBtn = document.getElementById('calculate-btn');
    const lmpInput = document.getElementById('lmp');
    const cycleLengthSelect = document.getElementById('cycle-length');
    const resultsDiv = document.getElementById('results');
    const lmpResult = document.getElementById('lmp-result');
    const eddResult = document.getElementById('edd-result');
    const currentPregnancyAge = document.getElementById('current-pregnancy-age');
    const pregnancyStage = document.getElementById('pregnancy-stage');
    const currentWeek = document.getElementById('current-week');
    const timelineWeeks = document.getElementById('timeline-weeks');
    const timelineNavBtns = document.querySelectorAll('.timeline-nav-btn');
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    // توليد بطاقات أسابيع الحمل
    function generateTimelineWeeks() {
        timelineWeeks.innerHTML = '';
        
        for (let i = 1; i <= 40; i++) {
            const weekCard = document.createElement('div');
            weekCard.className = 'week-card';
            weekCard.dataset.week = i;
            
            let trimester = '';
            let description = '';
            
            if (i <= 12) {
                trimester = 'الثلث الأول';
                description = 'تكوين الأعضاء الأساسية';
            } else if (i <= 26) {
                trimester = 'الثلث الثاني';
                description = 'تطور الأعضاء والحركة';
            } else {
                trimester = 'الثلث الثالث';
                description = 'اكتساب الوزن والاستعداد للولادة';
            }
            
            weekCard.innerHTML = `
                <h4>الأسبوع ${i}</h4>
                <p>${trimester}</p>
                <p class="week-desc">${description}</p>
            `;
            
            timelineWeeks.appendChild(weekCard);
        }
    }

    // حساب موعد الولادة المتوقع
    function calculateEDD(lmpDate, cycleLength) {
        // حساب تاريخ الإخصاب التقريبي
        const conceptionDate = new Date(lmpDate);
        conceptionDate.setDate(conceptionDate.getDate() + (cycleLength - 14));
        
        // حساب موعد الولادة المتوقع (280 يوم من LMP أو 266 يوم من الإخصاب)
        const eddFromLMP = new Date(lmpDate);
        eddFromLMP.setDate(eddFromLMP.getDate() + 280);
        
        const eddFromConception = new Date(conceptionDate);
        eddFromConception.setDate(eddFromConception.getDate() + 266);
        
        // نأخذ المتوسط بين الطريقتين لزيادة الدقة
        const eddTimestamp = (eddFromLMP.getTime() + eddFromConception.getTime()) / 2;
        const edd = new Date(eddTimestamp);
        
        return edd;
    }

    // حساب عمر الحمل الحالي
    function getPregnancyAge(lmpDate) {
        const today = new Date();
        const diffTime = today - new Date(lmpDate);
        const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
        
        const weeks = Math.floor(diffDays / 7);
        const days = diffDays % 7;
        
        return { weeks, days };
    }

    // تحديد مرحلة الحمل
    function getPregnancyStage(weeks) {
        if (weeks <= 12) {
            return 'الثلث الأول';
        } else if (weeks <= 26) {
            return 'الثلث الثاني';
        } else if (weeks <= 40) {
            return 'الثلث الثالث';
        } else {
            return 'انتهاء فترة الحمل المتوقعة';
        }
    }

    // تنسيق التاريخ لعرضه
    function formatDate(date) {
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        return date.toLocaleDateString('ar-EG', options);
    }

    // تصفية أسابيع الحمل حسب المرحلة
    function filterTimelineWeeks(weeksRange) {
        const weekCards = document.querySelectorAll('.week-card');
        
        weekCards.forEach(card => {
            const weekNum = parseInt(card.dataset.week);
            
            if (weeksRange === 'all') {
                card.style.display = 'block';
            } else {
                const [start, end] = weeksRange.split('-').map(Number);
                card.style.display = (weekNum >= start && weekNum <= end) ? 'block' : 'none';
            }
        });
    }

    // تمييز الأسبوع الحالي في الجدول الزمني
    function highlightCurrentWeek(weekNum) {
        const weekCards = document.querySelectorAll('.week-card');
        
        weekCards.forEach(card => {
            card.classList.remove('active');
            
            if (parseInt(card.dataset.week) === weekNum) {
                card.classList.add('active');
                card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        });
    }

    // معالجة النقر على زر الحساب
    calculateBtn.addEventListener('click', function() {
        if (!lmpInput.value) {
            alert('الرجاء إدخال تاريخ آخر دورة شهرية');
            return;
        }
        
        const lmpDate = lmpInput.value;
        const cycleLength = parseInt(cycleLengthSelect.value);
        
        // حساب موعد الولادة
        const edd = calculateEDD(lmpDate, cycleLength);
        
        // حساب عمر الحمل الحالي
        const pregnancyAge = getPregnancyAge(lmpDate);
        
        // تحديد مرحلة الحمل
        const stage = getPregnancyStage(pregnancyAge.weeks);
        
        // عرض النتائج
        lmpResult.textContent = formatDate(new Date(lmpDate));
        eddResult.textContent = formatDate(edd);
        currentPregnancyAge.textContent = `${pregnancyAge.weeks} أسابيع و ${pregnancyAge.days} أيام`;
        pregnancyStage.textContent = stage;
        currentWeek.textContent = pregnancyAge.weeks;
        
        // عرض النتائج وتمييز الأسبوع الحالي
        resultsDiv.style.display = 'block';
        highlightCurrentWeek(pregnancyAge.weeks);
        
        // تصفية الجدول الزمني حسب المرحلة الحالية
        let weeksRange = 'all';
        if (pregnancyAge.weeks <= 12) {
            weeksRange = '1-12';
        } else if (pregnancyAge.weeks <= 26) {
            weeksRange = '13-26';
        } else if (pregnancyAge.weeks <= 40) {
            weeksRange = '27-40';
        }
        
        filterTimelineWeeks(weeksRange);
        document.querySelector(`.timeline-nav-btn[data-week="${weeksRange}"]`).click();
    });

    // معالجة النقر على أزرار تصفية الجدول الزمني
    timelineNavBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            timelineNavBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            const weeksRange = this.dataset.week;
            filterTimelineWeeks(weeksRange);
        });
    });

    // معالجة النقر على أزرار التبويب
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            tabBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            const tabId = this.dataset.tab;
            tabContents.forEach(content => {
                content.classList.remove('active');
                if (content.id === tabId) {
                    content.classList.add('active');
                }
            });
        });
    });

    // معالجة النقر على بطاقات الأسابيع
    timelineWeeks.addEventListener('click', function(e) {
        const weekCard = e.target.closest('.week-card');
        if (weekCard) {
            const weekNum = parseInt(weekCard.dataset.week);
            
            // تمييز البطاقة المحددة
            document.querySelectorAll('.week-card').forEach(card => {
                card.classList.remove('active');
            });
            weekCard.classList.add('active');
            
            // عرض معلومات الأسبوع المحدد
            alert(`معلومات الأسبوع ${weekNum}:\n\nهذا الأسبوع جزء من ${weekCard.querySelector('p').textContent}\n\n${weekCard.querySelector('.week-desc').textContent}`);
        }
    });

    // تعيين تاريخ افتراضي (اليوم) لحقل الإدخال
    const today = new Date();
    const formattedToday = today.toISOString().split('T')[0];
    lmpInput.value = formattedToday;
    
    // توليد الجدول الزمني عند تحميل الصفحة
    generateTimelineWeeks();
});