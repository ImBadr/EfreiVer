# Serveur de websockets

Ce serveur va gérer toutes les parties interactions en temps réel entre les utilisateurs. Le principe est simple, l'utilisateur et le serveur vont s'échanger des "paquets", autrement dit des informations caractérisant une action : envoyer un message, envoyer un signalement, définir un prix etc...

# Authentification

Ce service est critique. Il est impensable de laisser la possibilité à n'importe qui d'envoyer des messages a la place de quelqu'un d'autres. Il faut donc le sécuriser ! Pour ce faire, au moment de la création du socket le client va devoir spécifier un JWT (Json Web Token). Ce token permet d'authentifier l'utilisateur et est vérifier par le serveur. Ainsi, on peut être certain de l'identitié de l'utilisateur qui communique avec le serveur.

## Token JWT

Il y a plusieurs façon de transmettre ce token au serveur :

- via un header **Authorization** (recommandé)
- via les cookies
- via un paramètre URL **token**

# Liste des paquets

_la fleche indique la direction du paquet, '<=' signifie du serveur vers le client et inversement '=>' du client vers le serveur_

### 0x1 <= PING

C'est un paquet qui est envoyé par le serveur pour vérifier si le client est toujours connecté. C'est un moyen simple de vérifier si la socket est toujours active. Sinon, par défaut, il y a un timeout lorsque la connexion drop. Mais c'est mieux si on gère nous même ce cas que de dépendre sur une implémentation quelconque.

Ce paquet est envoyé toutes les 5 secondes par le serveur.

### 0x2 => PONG

Lorsque que le client va recevoir le paquet OxO1 - PING, il va répondre par un PONG. Le serveur, ainsi, pourra vérifier si le client est toujours bien connecté.

En cas de non réponse pendant 30 secondes, le client est considéré comme étant déconnecté.

### 0x05 => ASK CONVERSATIONS

### 0x06 <= RETURN CONVERSATIONS

### 0x07 => CREATE MSG

### 0x08 <= STATUS MSG

### 0x09 => OPENED CONVERSATION (SEEN)

Permet de notifier le serveur que l'utilisateur vient d'ouvrir une conversation. Et donc, de lire les messages. Permet ainsi d'envoyer un 0x11 pour modifier le status de lecture du message.

| clé          | description           |
| ------------ | --------------------- |
| conversation | id de la conversation |

### 0x10 <= ALERT NEW MSG

Permet de notifier l'utilisateur qu'il a reçu un nouveau message

### 0x11 <= MSG SEEN

Indique au client qu'un de ses messages vient d'être lu par son destinataire.

| clé          | description                       |
| ------------ | --------------------------------- |
| conversation | id de la conversation             |
| message      | id du message qui vient d'être lu |

### 0xE0 <= UPDATE CONVERSATION DATAS

### 0xE1 => SERVICE CREATED

### 0xE2 => NOTE SUBMITTED

### 0xE3 => LITIGE CREATED

### 0xE4 => SERVICE TERMINATED

### 0xE5 => PRICE EDITED

### 0xE6 => NEGOTIATION END

### 0xFE => REPORT CONVERSATIONS

### 0xFF => REPORT MSG
