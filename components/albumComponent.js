function createAlbumElement(album, isFavourite = false, token = '') {
    const slide = document.createElement('div');
    slide.classList.add('swiper-slide');
    slide.innerHTML = `
        <div class="relative group overflow-hidden rounded-lg shadow-lg w-64 h-64 mx-auto">
            <button onclick="toggleFavourite('${album._id}', '${token || ''}')"
                    class="absolute top-2 right-2 ${isFavourite ? 'text-red-500' : 'text-gray-500'} hover:text-red-500 focus:outline-none">
                <i class="bi bi-${isFavourite ? 'heart-fill' : 'heart'} text-2xl" id="heart-${album._id}"></i>
            </button>
            <img src="${album.cover_image}" alt="${album.title}" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-white/70 to-transparent group-hover:translate-y-full transition-transform duration-500">
                <div class="absolute bottom-0 p-4 text-left">
                    <h3 class="text-sm font-bold text-gray-800 mb-1">${album.title}</h3>
                    <p class="text-ss text-gray-600">${album.artist_id.name}</p>
                </div>
            </div>
        </div>
    `;

    slide.addEventListener('click', (event) => {
        if (!event.target.closest('button')) {
            window.location.href = `/album/${album._id}`;
        }
    });

    return slide;
}

function createPlaylistElement(playlist, isFavourite, isOwner, token) {
    const slide = document.createElement('div');
    slide.classList.add('swiper-slide');
    slide.innerHTML = `
        <div class="relative group overflow-hidden rounded-lg shadow-lg w-64 h-64 mx-auto">
            ${isOwner
        ? `<button onclick="openEdit('${playlist._id}')"
                    class="absolute top-2 left-2 text-orange-400 hover:text-red-500 focus:outline-none transition-all ease-in-out">
                    <i class="bi bi-pen text-2xl" style="transform: scaleX(-1) !important;"></i>
                </button>` : ''}
             <button onclick="togglePlaylistFavourite('${playlist._id}', '${token || ''}')"
                    class="absolute top-2 right-2 ${isFavourite ? 'text-red-500' : 'text-gray-500'} hover:text-red-500 focus:outline-none">
                    <i class="bi bi-${isFavourite ? 'heart-fill' : 'heart'} text-2xl" id="playlist-heart-${playlist._id}"></i>
            </button>
            <img src="${playlist.cover_image}" alt="${playlist.name}" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-white/70 to-transparent group-hover:translate-y-full transition-transform duration-500">
                <div class="absolute bottom-0 p-4 text-left">
                    <h3 class="text-sm font-bold text-gray-800 mb-1">${playlist.name}</h3>
                </div>
            </div>
        </div>
    `;

    slide.addEventListener('click', (event) => {
        if (!event.target.closest('button')) {
            window.location.href = `/playlist/${playlist._id}`;
        }
    });

    return slide;
}

function toggleFavourite(albumId, token) {
    if (!token) {
        window.location.href = '/connect';
        return;
    }

    fetch('http://localhost:3000/api/favourite-album', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
        },
        body: JSON.stringify({albumId}),
    })
        .then(response => response.json())
        .then(result => {
            if (result.message) {
                const heartIcon = document.getElementById(`heart-${albumId}`);
                if (result.message.includes('ajouté')) {
                    heartIcon.classList.add('text-red-500');
                    heartIcon.classList.add('bi-heart-fill');
                    heartIcon.classList.remove('text-gray-500');
                    heartIcon.classList.remove('bi-heart');
                } else {
                    heartIcon.classList.add('text-gray-500');
                    heartIcon.classList.remove('bi-heart-fill');
                    heartIcon.classList.add('bi-heart');
                    heartIcon.classList.remove('text-red-500');
                }
            }
        })
        .catch(error => console.error('Erreur lors de la gestion des favoris :', error));
}

function togglePlaylistFavourite(playlistId, token) {
    if (!token) {
        window.location.href = '/connect';
        return;
    }

    fetch('http://localhost:3000/api/favourite-playlist', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
        },
        body: JSON.stringify({ playlistId }),
    })
        .then(response => response.json())
        .then(result => {
            if (result.message) {
                const heartIcon = document.getElementById(`playlist-heart-${playlistId}`);
                if (result.message.includes('ajoutée')) {
                    heartIcon.classList.add('text-red-500');
                    heartIcon.classList.remove('text-gray-500');
                    heartIcon.classList.replace('bi-heart', 'bi-heart-fill');
                } else {
                    heartIcon.classList.add('text-gray-500');
                    heartIcon.classList.remove('text-red-500');
                    heartIcon.classList.replace('bi-heart-fill', 'bi-heart');
                }
            }
        })
        .catch(error => console.error('Erreur lors de la gestion des favoris :', error));
}


function openEdit(playlistId) {
    window.location.href = `/playlist/${playlistId}/edit`;
}