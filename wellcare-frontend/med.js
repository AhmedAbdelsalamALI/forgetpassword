document.getElementById('drugComparisonForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const drug1 = document.getElementById('drug1').value.trim();
    const drug2 = document.getElementById('drug2').value.trim();
    const includePrice = document.getElementById('includePrice').checked;

    // Show loading spinner
    document.getElementById('loading').style.display = 'block';
    document.getElementById('comparisonResult').style.display = 'none';
    document.getElementById('errorMessage').style.display = 'none';

    setTimeout(() => {
        document.getElementById('loading').style.display = 'none';

        try {
            const drug1Data = getDrugData(drug1);
            const drug2Data = getDrugData(drug2);

            const drugNotFound = isDrugUnknown(drug1Data) || isDrugUnknown(drug2Data);

            if (drugNotFound) {
                const missingDrug = isDrugUnknown(drug1Data) ? drug1 : drug2;
                document.getElementById('errorMessage').style.display = 'block';
                document.getElementById('errorMessage').innerHTML = `
                    <i class="fas fa-info-circle"></i>
                    <p>⚠️ الدواء "<strong>${missingDrug}</strong>" غير متوفر حالياً، وسيتم إضافته مستقبلاً بإذن الله.</p>
                `;
                return;
            }

            // Display drug names
            document.getElementById('drug1Name').textContent = drug1;
            document.getElementById('drug2Name').textContent = drug2;

            // Display comparison data
            document.getElementById('drug1ActiveIngredient').textContent = drug1Data.activeIngredient;
            document.getElementById('drug2ActiveIngredient').textContent = drug2Data.activeIngredient;

            document.getElementById('drug1Uses').textContent = drug1Data.uses;
            document.getElementById('drug2Uses').textContent = drug2Data.uses;

            document.getElementById('drug1Contraindications').textContent = drug1Data.contraindications;
            document.getElementById('drug2Contraindications').textContent = drug2Data.contraindications;

            document.getElementById('drug1SideEffects').textContent = drug1Data.sideEffects;
            document.getElementById('drug2SideEffects').textContent = drug2Data.sideEffects;

            document.getElementById('drug1Interactions').textContent = drug1Data.interactions;
            document.getElementById('drug2Interactions').textContent = drug2Data.interactions;

            if (includePrice) {
                document.getElementById('priceRow').style.display = 'flex';
                document.getElementById('drug1Price').textContent = drug1Data.price || 'غير متوفر';
                document.getElementById('drug2Price').textContent = drug2Data.price || 'غير متوفر';
            } else {
                document.getElementById('priceRow').style.display = 'none';
            }

            // Show results
            document.getElementById('comparisonResult').style.display = 'block';
        } catch (error) {
            console.error('Error:', error);
            document.getElementById('errorMessage').style.display = 'block';
        }
    }, 1500);
});

function resetForm() {
    document.getElementById('drugComparisonForm').reset();
    document.getElementById('comparisonResult').style.display = 'none';
    document.getElementById('errorMessage').style.display = 'none';
}

// Helper: Check if drug data is placeholder
function isDrugUnknown(drugData) {
    return drugData.activeIngredient === 'غير معروف' &&
           drugData.uses === 'غير معروف';
}

// Drug database (mock)
function getDrugData(drugName) {
    const drugDatabase = {
        'بنادول': {
            activeIngredient: 'باراسيتامول (500mg)',
            uses: 'تسكين الآلام وخفض الحرارة',
            contraindications: 'الحساسية للباراسيتامول، أمراض الكبد الشديدة',
            sideEffects: 'نادراً ما يسبب طفح جلدي، غثيان',
            interactions: 'يزيد خطر تلف الكبد مع الكحول، قد يتفاعل مع الوارفارين',
            price: '15 ريال'
        },
        'فيفادول': {
            activeIngredient: 'باراسيتامول (500mg)',
            uses: 'تسكين الآلام وخفض الحرارة',
            contraindications: 'الحساسية للباراسيتامول، أمراض الكبد',
            sideEffects: 'نادراً ما يسبب اضطرابات في الجهاز الهضمي',
            interactions: 'يزيد خطر تلف الكبد مع الكحول',
            price: '12 ريال'
        },
        'بروفين': {
            activeIngredient: 'آيبوبروفين (400mg)',
            uses: 'تسكين الآلام، مضاد للالتهاب، خفض الحرارة',
            contraindications: 'قرحة المعدة، أمراض الكلى، الربو',
            sideEffects: 'حرقة في المعدة، دوخة، طفح جلدي',
            interactions: 'يتفاعل مع مميعات الدم، مدرات البول',
            price: '20 ريال'
        },
        'أكامول': {
            activeIngredient: 'باراسيتامول (500mg)',
            uses: 'تسكين الآلام وخفض الحرارة',
            contraindications: 'الحساسية للباراسيتامول، أمراض الكبد',
            sideEffects: 'نادراً ما يسبب آثار جانبية',
            interactions: 'قد يتفاعل مع بعض أدوية الصرع',
            price: '10 ريال'
        }
    };

    return drugDatabase[drugName] || {
        activeIngredient: 'غير معروف',
        uses: 'غير معروف',
        contraindications: 'غير معروف',
        sideEffects: 'غير معروف',
        interactions: 'غير معروف',
        price: 'غير متوفر'
    };
}
