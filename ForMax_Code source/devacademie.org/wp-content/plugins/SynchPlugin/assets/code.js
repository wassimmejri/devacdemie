document.addEventListener('DOMContentLoaded', function () {
    document.body.addEventListener('click', function (e) {
        const btn = e.target.closest('.inscription-btn');
        const category = e.target.closest('.moodle-category');

        if (btn) {
            e.preventDefault();
            const courseId = btn.getAttribute('data-course-id');

            fetch(moodle_ajax.ajax_url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    action: 'inscrire_utilisateur_moodle',
                    course_id: courseId
                })
            })
            .then(response => response.json())
            .then(data => {
                alert(data.data.message);
                if (data.success && data.data.redirect_url) {
                    setTimeout(() => {
                        window.location.href = data.data.redirect_url;
                    }, 1000);
                }
            });
        }

        if (category) {
            e.preventDefault();
            const categoryId = category.getAttribute('data-id');

            const xhr = new XMLHttpRequest();
            xhr.open("POST", moodle_ajax.ajax_url, true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onload = function () {
                if (xhr.status === 200) {
                    document.getElementById("moodle-courses").innerHTML = xhr.responseText;
                } else {
                    console.error("Erreur AJAX: " + xhr.statusText);
                }
            };

            xhr.onerror = function () {
                console.error("Erreur de connexion AJAX.");
            };

            xhr.send("action=get_courses_by_category&category_id=" + categoryId);
        }
    });
});







// document.addEventListener('DOMContentLoaded', function() {
//     document.querySelectorAll('.inscription-btn').forEach(function(btn) {
//         btn.addEventListener('click', function(e) {
//             e.preventDefault();
            
//             // Sauvegarder le texte original et désactiver le bouton
//             const originalText = btn.innerHTML;
//             btn.innerHTML = '<span class="spinner"></span> Traitement...';
//             btn.disabled = true;
            
//             // Supprimer les messages précédents
//             document.querySelectorAll('.moodle-message').forEach(el => el.remove());
            
//             const courseId = this.getAttribute('data-course-id');
//             const nonce = moodle_ajax.nonce;

//             fetch(moodle_ajax.ajax_url, {
//                 method: 'POST',
//                 headers: {
//                     'Content-Type': 'application/x-www-form-urlencoded',
//                     'Accept': 'application/json'
//                 },
//                 body: new URLSearchParams({
//                     action: 'inscrire_utilisateur_moodle',
//                     course_id: courseId,
//                     security: nonce
//                 })
//             })
//             .then(response => {
//                 if (!response.ok) {
//                     throw new Error(`Erreur HTTP: ${response.status}`);
//                 }
//                 return response.json();
//             })
//             .then(data => {
//                 // Réactiver le bouton
//                 btn.innerHTML = originalText;
//                 btn.disabled = false;
                
//                 if (!data.success) {
//                     throw new Error(data.data?.message || 'Erreur lors de l\'inscription');
//                 }

//                 // Afficher un message de succès stylé
//                 const messageEl = document.createElement('div');
//                 messageEl.className = 'moodle-message success animated fadeIn';
//                 messageEl.innerHTML = `
//                     <svg viewBox="0 0 24 24" width="24" height="24">
//                         <path fill="currentColor" d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
//                     </svg>
//                     <span>${data.data.message || 'Inscription réussie !'}</span>
//                 `;
                
//                 // Insérer le message après le bouton
//                 btn.parentNode.insertBefore(messageEl, btn.nextSibling);

//                 // Redirection si URL disponible
//                 if (data.data.redirect_url) {
//                     // Ajouter un délai pour permettre la lecture du message
//                     setTimeout(() => {
//                         // Ajouter une animation de sortie
//                         messageEl.classList.remove('fadeIn');
//                         messageEl.classList.add('fadeOut');
                        
//                         // Rediriger après l'animation
//                         setTimeout(() => {
//                             window.location.href = data.data.redirect_url;
//                         }, 500);
//                     }, 2500);
//                 }
//             })
//             .catch(error => {
//                 console.error('Erreur:', error);
                
//                 // Réactiver le bouton
//                 btn.innerHTML = originalText;
//                 btn.disabled = false;
                
//                 // Afficher un message d'erreur stylé
//                 const errorEl = document.createElement('div');
//                 errorEl.className = 'moodle-message error animated shake';
//                 errorEl.innerHTML = `
//                     <svg viewBox="0 0 24 24" width="24" height="24">
//                         <path fill="currentColor" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
//                     </svg>
//                     <span>${error.message || 'Une erreur est survenue'}</span>
//                 `;
                
//                 btn.parentNode.insertBefore(errorEl, btn.nextSibling);
                
//                 // Supprimer le message après 5 secondes
//                 setTimeout(() => {
//                     errorEl.classList.remove('shake');
//                     errorEl.classList.add('fadeOut');
//                     setTimeout(() => errorEl.remove(), 500);
//                 }, 5000);
//             });
//         });
//     });
// });







// document.addEventListener('DOMContentLoaded', function () {
//     if (window.location.hash === '#_=_') {
//         history.replaceState(null, null, window.location.pathname);

//         // Appel Ajax pour récupérer l'URL de redirection
//         fetch('/wp-admin/admin-ajax.php?action=custom_social_redirect')
//             .then(response => response.json())
//             .then(data => {
//                 if (data.url) {
//                     window.location.href = data.url;
//                 }
//             });
//     }
// });



























//cours deja inscrit
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-action').forEach(function (button) {
        button.addEventListener('click', function (e) {
            e.preventDefault();

            const courseId = this.dataset.courseId;
            const action = this.dataset.action;

            if (!courseId || !action) {
                alert('Données manquantes.');
                return;
            }

            fetch(moodle_ajax.ajax_url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    action: action,
                    course_id: courseId
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.data.message);
                        window.location.href = data.data.redirect_url;
                    } else {
                        alert(data.data.message || 'Erreur.');
                    }
                })
                .catch(error => {
                    console.error('Erreur AJAX:', error);
                    alert('Erreur serveur.');
                });
        });
    });
});




//code asma




