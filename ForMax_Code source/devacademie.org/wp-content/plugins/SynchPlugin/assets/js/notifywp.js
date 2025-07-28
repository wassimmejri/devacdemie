document.addEventListener('DOMContentLoaded', function() {
    // Créer l'icône de notification
    const bellContainer = document.createElement('div');
    bellContainer.id = 'notifywp-bell';
    bellContainer.style.position = 'fixed';
    bellContainer.style.top = '20px';
    bellContainer.style.right = '20px';
    bellContainer.style.zIndex = '9999';
    bellContainer.style.cursor = 'pointer';
    
    // Icône de cloche SVG
    bellContainer.innerHTML = `
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
        </svg>
        <span id="notifywp-badge" style="
            position: absolute;
            top: -5px;
            right: -5px;
            background: red;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        ">0</span>
    `;
    
    document.body.appendChild(bellContainer);
    
    // Créer le popup de notifications
    const popup = document.createElement('div');
    popup.id = 'notifywp-popup';
    popup.style.position = 'fixed';
    popup.style.top = '60px';
    popup.style.right = '20px';
    popup.style.width = '300px';
    popup.style.maxHeight = '400px';
    popup.style.overflowY = 'auto';
    popup.style.backgroundColor = 'white';
    popup.style.border = '1px solid #ddd';
    popup.style.borderRadius = '4px';
    popup.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
    popup.style.padding = '10px';
    popup.style.display = 'none';
    popup.style.zIndex = '10000';
    document.body.appendChild(popup);
    
    const badge = document.getElementById('notifywp-badge');
    
    // Fonction pour charger les notifications
    async function loadNotifications() {
        try {
            const response = await fetch(notifywpData.restUrl + 'user-notifications', {
                headers: {
                    'X-WP-Nonce': notifywpData.nonce
                }
            });
            
            if (!response.ok) throw new Error('Erreur de réseau');
            
            const notifications = await response.json();
            
            // Mettre à jour le badge
            badge.textContent = notifications.length;
            badge.style.display = notifications.length > 0 ? 'flex' : 'none';
            
            // Mettre à jour le popup
            if (notifications.length > 0) {
                popup.innerHTML = notifications.map(notif => `
                    <div class="notifywp-notification" style="
                        padding: 8px;
                        margin-bottom: 8px;
                        border-bottom: 1px solid #eee;
                        background: ${notif.read ? '#fff' : '#f8f9fa'};
                    ">
                        <div style="font-weight: bold;">${notif.message}</div>
                        <div style="font-size: 12px; color: #666; margin-top: 4px;">
                            ${new Date(notif.date).toLocaleString()}
                        </div>
                    </div>
                `).join('');
            } else {
                popup.innerHTML = '<div style="padding: 10px; text-align: center;">Aucune nouvelle notification</div>';
            }
        } catch (error) {
            console.error('Erreur:', error);
            popup.innerHTML = '<div style="padding: 10px; color: red;">Erreur de chargement des notifications</div>';
        }
    }
    
    // Gestionnaire de clic sur la cloche
    bellContainer.addEventListener('click', async function(e) {
        e.stopPropagation();
        
        if (popup.style.display === 'none') {
            popup.style.display = 'block';
            await loadNotifications();
            
            // Marquer comme lues
            try {
                await fetch(notifywpData.restUrl + 'mark-read', {
                    method: 'POST',
                    headers: {
                        'X-WP-Nonce': notifywpData.nonce,
                        'Content-Type': 'application/json'
                    }
                });
                
                badge.style.display = 'none';
            } catch (error) {
                console.error('Erreur:', error);
            }
        } else {
            popup.style.display = 'none';
        }
    });
    
    // Fermer le popup quand on clique ailleurs
    document.addEventListener('click', function() {
        popup.style.display = 'none';
    });
    
    // Empêcher la fermeture quand on clique dans le popup
    popup.addEventListener('click', function(e) {
        e.stopPropagation();
    });
    
    // Charger les notifications au démarrage
    loadNotifications();
    
    // Vérifier les nouvelles notifications toutes les minutes
    setInterval(loadNotifications, 60000);
});