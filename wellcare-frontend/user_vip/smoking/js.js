document.addEventListener('DOMContentLoaded', function() {
    // عناصر واجهة المستخدم
    const daysCleanEl = document.getElementById('days-clean');
    const cigarettesSavedEl = document.getElementById('cigarettes-saved');
    const moneySavedEl = document.getElementById('money-saved');
    const motivationTextEl = document.getElementById('motivation-text');
    const cravingNowBtn = document.getElementById('craving-now');
    const distractionTipsEl = document.getElementById('distraction-tips');
    const cravingTimerEl = document.getElementById('craving-timer');
    const addCigaretteBtn = document.getElementById('add-cigarette-btn');
    const dailyTipTextEl = document.getElementById('daily-tip-text');
    const progressCircles = document.querySelectorAll('.progress-circle');
    const navBtns = document.querySelectorAll('.nav-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    // بيانات المستخدم (يمكن استبدالها بحفظ في localStorage)
    let userData = {
        quitDate: new Date(), // تاريخ الإقلاع (افتراضيًا اليوم)
        cigarettesPerDay: 10, // عدد السجائر يوميًا
        cigarettePrice: 0.5, // سعر السيجارة بالدولار
        lastCigarette: null // تاريخ آخر سيجارة
    };

    // تحميل البيانات المحفوظة
    function loadUserData() {
        const savedData = localStorage.getItem('smokingCessationAppData');
        if (savedData) {
            userData = JSON.parse(savedData);
            userData.quitDate = new Date(userData.quitDate);
            if (userData.lastCigarette) {
                userData.lastCigarette = new Date(userData.lastCigarette);
            }
        }
    }

    // حفظ البيانات
    function saveUserData() {
        localStorage.setItem('smokingCessationAppData', JSON.stringify(userData));
    }

    // حساب الإحصائيات
    function calculateStats() {
        const now = new Date();
        const quitDate = new Date(userData.quitDate);
        
        // حساب الأيام بدون تدخين
        const timeDiff = now - quitDate;
        const daysClean = Math.floor(timeDiff / (1000 * 60 * 60 * 24));
        
        // حساب السجائر التي تم توفيرها
        const cigarettesSaved = daysClean * userData.cigarettesPerDay;
        
        // حساب التوفير المالي
        const moneySaved = cigarettesSaved * userData.cigarettePrice;
        
        return {
            daysClean,
            cigarettesSaved,
            moneySaved
        };
    }

    // تحديث واجهة المستخدم
    function updateUI() {
        const stats = calculateStats();
        
        // تحديث الإحصائيات
        daysCleanEl.textContent = stats.daysClean;
        cigarettesSavedEl.textContent = stats.cigarettesSaved;
        moneySavedEl.textContent = stats.moneySaved.toFixed(2);
        
        // تحديث الرسالة التحفيزية
        updateMotivationalMessage(stats.daysClean);
        
        // تحديث التقدم الصحي
        updateHealthProgress(stats.daysClean);
        
        // تحديث نصيحة اليوم
        updateDailyTip();
    }

    // تحديث الرسالة التحفيزية
    function updateMotivationalMessage(daysClean) {
        let message = '';
        
        if (daysClean === 0) {
            message = 'اليوم هو بداية رحلتك! أنت تتخذ قرارًا سيغير حياتك.';
        } else if (daysClean < 3) {
            message = 'الأيام الأولى هي الأصعب، لكنك تقوم بعمل رائع! استمر.';
        } else if (daysClean < 7) {
            message = 'أسبوع كامل تقريبًا! جسمك يبدأ في التعافي.';
        } else if (daysClean < 30) {
            message = 'أنت على الطريق الصحيح! استمر في التقدم.';
        } else if (daysClean < 90) {
            message = 'شهر كامل! صحتك تتحسن بشكل ملحوظ.';
        } else if (daysClean < 180) {
            message = '6 أشهر! خطر الإصابة بأمراض القلب انخفض إلى النصف.';
        } else if (daysClean < 365) {
            message = 'قريبًا سنة كاملة! خطر السكتة الدماغية مثل غير المدخنين.';
        } else {
            message = 'مذهل! خطر الوفاة المبكرة انخفض بنسبة 50٪ تقريبًا.';
        }
        
        motivationTextEl.textContent = message;
    }

    // تحديث التقدم الصحي
    function updateHealthProgress(daysClean) {
        // حساب النسب المئوية للتقدم الصحي
        const lungCapacity = Math.min(100, Math.floor(daysClean * 0.5));
        const heartHealth = Math.min(100, Math.floor(daysClean * 0.3));
        const energyLevel = Math.min(100, Math.floor(daysClean * 0.4));
        
        // تحديث دوائر التقدم
        progressCircles.forEach(circle => {
            const progressType = circle.parentElement.id;
            let progressValue = 0;
            
            switch(progressType) {
                case 'lung-capacity':
                    progressValue = lungCapacity;
                    break;
                case 'heart-health':
                    progressValue = heartHealth;
                    break;
                case 'energy-level':
                    progressValue = energyLevel;
                    break;
            }
            
            circle.style.background = `conic-gradient(var(--secondary-color) ${progressValue}%, #eee ${progressValue}%)`;
            circle.querySelector('span').textContent = `${progressValue}%`;
            circle.dataset.progress = progressValue;
        });
        
        // تحديث الإنجازات الصحية
        updateMilestones(daysClean);
    }

    // تحديث الإنجازات الصحية
    function updateMilestones(daysClean) {
        const milestones = document.querySelectorAll('.milestone');
        
        milestones.forEach(milestone => {
            const text = milestone.querySelector('p').textContent;
            
            if (text.includes('20 دقيقة') && daysClean >= 0) {
                milestone.classList.add('achieved');
                milestone.querySelector('i').className = 'fas fa-check-circle';
            }
            
            if (text.includes('8 ساعات') && daysClean >= 1) {
                milestone.classList.add('achieved');
                milestone.querySelector('i').className = 'fas fa-check-circle';
            }
            
            if (text.includes('24 ساعة') && daysClean >= 1) {
                milestone.classList.add('achieved');
                milestone.querySelector('i').className = 'fas fa-check-circle';
            }
        });
    }

    // تحديث نصيحة اليوم
    function updateDailyTip() {
        const tips = [
            "اشرب الكثير من الماء لمساعدة جسمك على التخلص من السموم.",
            "مارس تمارين التنفس العميق عند الشعور بالرغبة في التدخين.",
            "تناول الفواكه الطازجة عند الشعور بالرغبة في التدخين.",
            "اخرج لنزهة قصيرة لتغيير الجو وتفريغ الطاقة.",
            "تذكر دائمًا أسباب إقلاعك عن التدخين.",
            "تجنب الأماكن والمواقف التي تذكرك بالتدخين.",
            "احتفل بإنجازاتك الصغيرة في رحلة الإقلاع.",
            "استخدم علكة خالية من السكر لإشغال فمك.",
            "مارس الرياضة بانتظام لتقليل التوتر.",
            "تحدث مع صديق أو أحد أفراد العائلة عندما تشعر بالرغبة الشديدة."
        ];
        
        const today = new Date().getDate();
        const dailyTip = tips[today % tips.length];
        dailyTipTextEl.textContent = dailyTip;
    }

    // بدء عداد الرغبة
    function startCravingTimer() {
        let minutes = 5;
        let seconds = 0;
        
        distractionTipsEl.classList.remove('hidden');
        
        const timer = setInterval(() => {
            if (seconds === 0) {
                if (minutes === 0) {
                    clearInterval(timer);
                    distractionTipsEl.classList.add('hidden');
                    return;
                }
                minutes--;
                seconds = 59;
            } else {
                seconds--;
            }
            
            cravingTimerEl.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        }, 1000);
    }

    // تبديل التبويبات
    function switchTab(tabId) {
        tabContents.forEach(content => {
            content.classList.remove('active');
            if (content.id === `${tabId}-tab`) {
                content.classList.add('active');
            }
        });
        
        navBtns.forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.tab === tabId) {
                btn.classList.add('active');
            }
        });
    }

    // معالجة أحداث النقر
    cravingNowBtn.addEventListener('click', startCravingTimer);
    
    addCigaretteBtn.addEventListener('click', () => {
        if (confirm('هل تريد حقًا تسجيل سيجارة؟ هذا سيؤثر على إحصائياتك.')) {
            userData.lastCigarette = new Date();
            saveUserData();
            updateUI();
            alert('تم تسجيل السيجارة. لا تثبط عزيمتك، يمكنك البدء من جديد!');
        }
    });
    
    navBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            switchTab(btn.dataset.tab);
        });
    });

    // تهيئة التطبيق
    loadUserData();
    updateUI();
    
    // تحريك دوائر التقدم بعد تحميل الصفحة
    setTimeout(() => {
        progressCircles.forEach(circle => {
            const progress = circle.dataset.progress;
            circle.style.background = `conic-gradient(var(--secondary-color) ${progress}%, #eee ${progress}%)`;
        });
    }, 500);
});