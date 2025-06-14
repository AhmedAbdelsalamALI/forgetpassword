// استدعاء العناصر
const modal = document.getElementById('videoModal');
const modalVideo = document.getElementById('modal-video');
const videoSource = document.getElementById('video-source');
const closeBtn = document.querySelector('.close-btn');
const videoCards1 = document.querySelectorAll('.video-card1');
const videoCards2 = document.querySelectorAll('.video-card2');
const videoCards3 = document.querySelectorAll('.video-card3');
const videoCards4 = document.querySelectorAll('.video-card4');
const videoCards5 = document.querySelectorAll('.video-card5');
const videoCards6 = document.querySelectorAll('.video-card6');
const videoCards7 = document.querySelectorAll('.video-card7');
const videoCards8 = document.querySelectorAll('.video-card8');
const videoCards9 = document.querySelectorAll('.video-card9');
const videoCards10 = document.querySelectorAll('.video-card10');
const videoCards11 = document.querySelectorAll('.video-card11');
const videoCards12 = document.querySelectorAll('.video-card12');
const videoCards13 = document.querySelectorAll('.video-card13');
const videoCards14 = document.querySelectorAll('.video-card14');
const videoCards15 = document.querySelectorAll('.video-card15');
// فتح المودال عند النقر على بطاقة فيديو
videoCards1.forEach(card => {
    card.addEventListener('click', () => {
        const videoSrc = "Medical Video/اضرار السجائر.mp4"; // مسار الفيديو المحلي
        modal.classList.add('show'); // إظهار المودال
        videoSource.src = videoSrc;
        modalVideo.load(); // تحميل الفيديو
        modalVideo.play(); // تشغيل الفيديو
    });
});

videoCards2.forEach(card => {
    card.addEventListener('click', () => {
        const videoSrc = "Medical Video/اضرار قلة النوم .mp4"; // مسار الفيديو المحلي
        modal.classList.add('show');
        videoSource.src = videoSrc;
        modalVideo.load();
        modalVideo.play();
    });
});

videoCards3.forEach(card => {
    card.addEventListener('click', () => {
        const videoSrc = "Medical Video/التهاب المفاصل.mp4"; // مسار الفيديو المحلي
        modal.classList.add('show');
        videoSource.src = videoSrc;
        modalVideo.load();
        modalVideo.play();
    });
});

videoCards4.forEach(card => {
    card.addEventListener('click', () => {
        const videoSrc = "Medical Video/الزهايمر.mp4"; // مسار الفيديو المحلي
        modal.classList.add('show');
        videoSource.src = videoSrc;
        modalVideo.load();
        modalVideo.play();
    });
});

videoCards5.forEach(card => {
    card.addEventListener('click', () => {
        const videoSrc = "Medical Video/الم اسفل الظهر.mp4"; // مسار الفيديو المحلي
        modal.classList.add('show');
        videoSource.src = videoSrc;
        modalVideo.load();
        modalVideo.play();
    });
});

videoCards6.forEach(card => {
    card.addEventListener('click', () => {
        const videoSrc = "Medical Video/انواع السرطان الاكثر شيوعا لدي الاطفال.mp4"; // مسار الفيديو المحلي
        modal.classList.add('show');
        videoSource.src = videoSrc;
        modalVideo.load();
        modalVideo.play();
    });
});

videoCards7.forEach(card => {
    card.addEventListener('click', () => {
        const videoSrc = "Medical Video/فحوصات هامه لصحة الرجل.mp4"; // مسار الفيديو المحلي
        modal.classList.add('show');
        videoSource.src = videoSrc;
        modalVideo.load();
        modalVideo.play();
    });
});

videoCards8.forEach(card => {
    card.addEventListener('click', () => {
        const videoSrc = "Medical Video/فرط الحركه.mp4"; // مسار الفيديو المحلي
        modal.classList.add('show');
        videoSource.src = videoSrc;
        modalVideo.load();
        modalVideo.play();
    });
});

videoCards9.forEach(card => {
    card.addEventListener('click', () => {
        const videoSrc = "Medical Video/متلازمة اسبرجر.mp4"; // مسار الفيديو المحلي
        modal.classList.add('show');
        videoSource.src = videoSrc;
        modalVideo.load();
        modalVideo.play();
    });
});

videoCards10.forEach(card => {
    card.addEventListener('click', () => {
        const videoSrc = "Medical Video/اليوم العالمي للعلاج الطبيعي.mp4"; // مسار الفيديو المحلي
        modal.classList.add('show');
        videoSource.src = videoSrc;
        modalVideo.load();
        modalVideo.play();
    });
});

videoCards11.forEach(card => {
    card.addEventListener('click', () => {
        const videoSrc = "Medical Video/أطعمة مفيدة للصحة.mp4"; // مسار الفيديو المحلي
        modal.classList.add('show');
        videoSource.src = videoSrc;
        modalVideo.load();
        modalVideo.play();
    });
});

videoCards12.forEach(card => {
    card.addEventListener('click', () => {
        const videoSrc = "Medical Video/اليوم العالمي للإسعافات الأولية.mp4"; // مسار الفيديو المحلي
        modal.classList.add('show');
        videoSource.src = videoSrc;
        modalVideo.load();
        modalVideo.play();
    });
});

videoCards13.forEach(card => {
    card.addEventListener('click', () => {
        const videoSrc = "Medical Video/نصائح للحفاظ على الصحة النفسية.mp4"; // مسار الفيديو المحلي
        modal.classList.add('show');
        videoSource.src = videoSrc;
        modalVideo.load();
        modalVideo.play();
    });
});

videoCards14.forEach(card => {
    card.addEventListener('click', () => {
        const videoSrc = "Medical Video/حساسية اللاكتوز  وأعراضه.mp4"; // مسار الفيديو المحلي
        modal.classList.add('show');
        videoSource.src = videoSrc;
        modalVideo.load();
        modalVideo.play();
    });
});

videoCards15.forEach(card => {
    card.addEventListener('click', () => {
        const videoSrc = "Medical Video/نصائح للوقاية من ارتفاع ضغط الدم.mp4"; // مسار الفيديو المحلي
        modal.classList.add('show');
        videoSource.src = videoSrc;
        modalVideo.load();
        modalVideo.play();
    });
});



// إغلاق المودال عند النقر على زر الإغلاق
closeBtn.addEventListener('click', () => {
    modal.classList.remove('show'); // إخفاء المودال
    modalVideo.pause(); // إيقاف الفيديو عند إغلاق المودال
    modalVideo.currentTime = 0; // إعادة الفيديو للبداية
});

// إغلاق المودال عند النقر خارج المودال
window.addEventListener('click', (e) => {
    if (e.target === modal) {
        modal.classList.remove('show'); // إخفاء المودال
        modalVideo.pause(); // إيقاف الفيديو
        modalVideo.currentTime = 0; // إعادة الفيديو للبداية
    }
});

// Elements
const searchInput = document.getElementById('searchInput');
const searchButton = document.getElementById('searchButton');
const videoCards = document.querySelectorAll('.video-card');

// Search functionality
searchButton.addEventListener('click', () => {
    const searchTerm = searchInput.value.toLowerCase().trim();
    
    videoCards.forEach(card => {
        const title = card.querySelector('h3').textContent.toLowerCase();
        const description = card.querySelector('p').textContent.toLowerCase();
        
        if (title.includes(searchTerm) || description.includes(searchTerm)) {
            card.style.display = 'block'; // Show matching cards
        } else {
            card.style.display = 'none'; // Hide non-matching cards
        }
    });
});

//  Search bar 
searchInput.addEventListener('input', () => {
    const searchTerm = searchInput.value.toLowerCase().trim();

    videoCards.forEach(card => {
        const title = card.querySelector('h3').textContent.toLowerCase();
        const description = card.querySelector('p').textContent.toLowerCase();

        if (title.includes(searchTerm) || description.includes(searchTerm)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
});
