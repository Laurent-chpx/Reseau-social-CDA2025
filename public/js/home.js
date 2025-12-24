let currentOffset = 0;
const limit = 3;
let currentDepartmentId = null;
let detectedDepartmentName = null;

function createEventCard(event) {
    const gradients = [
        'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
        'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
        'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
        'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
        'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
        'linear-gradient(135deg, #30cfd0 0%, #330867 100%)',
        'linear-gradient(135deg, #a8edea 0%, #fed6e3 100%)',
        'linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%)'
    ];
    const gradient = gradients[event.id % 8];

    const promotedBadge = event.isPromoted
        ? '<span class="event-card__badge badge badge--promoted"><i class="fa-solid fa-star"></i> Sponsorisé</span>'
        : '';

    const promotedClass = event.isPromoted ? 'event-card--promoted' : '';

    let imageHtml;
    if (event.image && !event.image.includes('placeholder')) {
        imageHtml = `<img src="${event.image}" alt="${event.title}" loading="lazy">`;
    } else {
        imageHtml = `
            <div class="event-card__placeholder" style="background: ${gradient};">
                <span class="event-card__placeholder-icon"><i class="fa-solid fa-map-location-dot"></i></span>
            </div>
        `;
    }

    const description = event.description
        ? `<p class="event-card__description">${event.description.length > 120 ? event.description.slice(0, 120) + '...' : event.description}</p>`
        : '';

    const categories = event.categories && event.categories.length > 0
        ? `<div class="event-card__tags">${event.categories.slice(0, 3).map(cat => `<span class="tag">${cat}</span>`).join('')}</div>`
        : '';

    return `
        <article class="event-card ${promotedClass}">
            ${promotedBadge}
            <div class="event-card__image">
                ${imageHtml}
            </div>
            <div class="event-card__content">
                <h3 class="event-card__title">${event.title}</h3>
                <div class="event-card__meta">
                    <span class="event-card__location">
                        <span class="event-card__icon"><i class="fa-solid fa-map-location-dot"></i></span> ${event.city}
                    </span>
                    <span class="event-card__date">
                        <span class="event-card__icon"><i class="fa-regular fa-calendar-days"></i>
                        </span> ${event.dateStart}
                    </span>
                </div>
                ${categories}
                ${description}
                <div class="event-card__actions">
                    <a href="/event/${event.id}" class="btn btn--primary btn--sm">Voir les détails</a>
                </div>
            </div>
        </article>
    `;
}

async function loadEvents(reset = false) {
    const container = document.getElementById('events-container');
    const loadMoreBtn = document.getElementById('load-more-btn');
    const noEventsMsg = document.getElementById('no-events-message');
    const loadingMsg = document.getElementById('loading-message');

    if (!container) {
        return;
    }

    if (reset) {
        currentOffset = 0;
        container.innerHTML = '';
    }

    const params = new URLSearchParams({
        offset: currentOffset,
        limit: limit
    });

    if (currentDepartmentId) {
        params.append('department', currentDepartmentId);
    }

    try {
        const response = await fetch(`/api/events?${params}`);
        const data = await response.json();

        if (loadingMsg) loadingMsg.remove();

        if (data.events.length === 0 && currentOffset === 0) {
            if (noEventsMsg) noEventsMsg.style.display = 'block';
            if (loadMoreBtn) loadMoreBtn.style.display = 'none';
        } else {
            if (noEventsMsg) noEventsMsg.style.display = 'none';
            data.events.forEach(event => {
                container.innerHTML += createEventCard(event);
            });

            currentOffset += data.events.length;
            if (loadMoreBtn) loadMoreBtn.style.display = data.hasMore ? 'inline-block' : 'none';
        }
    } catch (error) {
        console.error('Erreur lors du chargement des événements:', error);
    }
}

function findDepartmentIdByCode(code) {
    const select = document.getElementById('department-select');
    if (!select) return null;

    const options = select.querySelectorAll('option[data-code]');

    for (const option of options) {
        if (option.dataset.code === code) {
            detectedDepartmentName = option.textContent.trim();
            return option.value;
        }
    }
    return null;
}

function updateLocationInfo(city, departmentName) {
    const locationInfo = document.getElementById('location-info');
    const title = document.getElementById('events-title');

    if (city && departmentName) {
        if (locationInfo) locationInfo.textContent = `📍 ${city} (${departmentName})`;
        if (title) title.textContent = `Événements dans le ${departmentName}`;
    } else {
        if (locationInfo) locationInfo.textContent = '';
        if (title) title.textContent = 'Tous les événements';
    }
}

async function initGeolocationByIP() {
    try {
        const response = await fetch('https://ipapi.co/json/');
        const data = await response.json();

        console.log('Localisation IP détectée :', {
            ville: data.city,
            région: data.region,
            codePostal: data.postal,
            pays: data.country_name
        });

        if (data.country_code === 'FR' && data.postal) {
            let departmentCode = data.postal.substring(0, 2);
            if (departmentCode === '97' || departmentCode === '98') {
                departmentCode = data.postal.substring(0, 3);
            }

            console.log('Code département détecté :', departmentCode);

            currentDepartmentId = findDepartmentIdByCode(departmentCode);

            if (currentDepartmentId) {
                const select = document.getElementById('department-select');
                if (select) select.value = currentDepartmentId;
                updateLocationInfo(data.city, detectedDepartmentName);
            } else {
                console.log('Département non trouvé en base');
                updateLocationInfo(null, null);
            }
        } else {
            console.log('Utilisateur hors de France ou code postal indisponible');
            updateLocationInfo(null, null);
        }
    } catch (error) {
        console.error('Erreur géolocalisation IP:', error);
        updateLocationInfo(null, null);
    }

    await loadEvents(true);
}

function initHomePage() {
    const container = document.getElementById('events-container');

    // Vérifier qu'on est sur la page d'accueil
    if (!container) {
        return;
    }

    console.log('Initialisation page accueil');

    // Réinitialiser les variables
    currentOffset = 0;
    currentDepartmentId = null;
    detectedDepartmentName = null;

    // Vider le container
    container.innerHTML = '<p class="events-grid__loading">Chargement des événements...</p>';

    // Gérer le bouton load more
    const loadMoreBtn = document.getElementById('load-more-btn');
    if (loadMoreBtn) {
        loadMoreBtn.onclick = () => loadEvents(false);
    }

    initGeolocationByIP();
}

// Support Turbo (si installé) + fallback DOMContentLoaded
document.addEventListener('turbo:load', initHomePage);
document.addEventListener('DOMContentLoaded', initHomePage);
