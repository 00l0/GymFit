document.addEventListener('DOMContentLoaded', function() {
        function showSection(sectionId) {
            const sections = document.querySelectorAll('.dashboard-section');
            sections.forEach(section => section.style.display = 'none');

            document.getElementById(sectionId).style.display = 'block';
        }


        window.showSection = showSection;
    });

