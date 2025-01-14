async function handleFormSubmit(event, action) {
    event.preventDefault();

    const formData = new FormData(event.target);
    const data = Object.fromEntries(formData.entries());

    const endpoint = action === 'register' ? '/auth/register' : '/auth/login';

    try {
        const response = await fetch(`http://localhost:3000${endpoint}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data),
        });

        const result = await response.json();

        if (response.ok) {
            alert(result.message);

            if (action === 'login' && result.user.session_token) {
                // Envoyer le token au backend PHP pour le stocker en session
                await fetch('/setSession', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ token: result.user.session_token }),
                });

                console.log('Token envoyé à PHP pour stockage en session.');
                window.location.href = '/'; // Rediriger après connexion
            }
        } else {
            alert(result.error);
        }
    } catch (error) {
        console.error('Erreur :', error);
        alert('Erreur de communication avec le serveur');
    }
}
