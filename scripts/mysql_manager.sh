#!/bin/bash

# Gestionnaire MySQL en ligne de commande - Wrapper Shell
# Usage: ./mysql_manager.sh [COMMANDE] [OPTIONS]

# Couleurs pour la console
RED='\033[31m'
GREEN='\033[32m'
YELLOW='\033[33m'
BLUE='\033[34m'
CYAN='\033[36m'
WHITE='\033[37m'
BOLD='\033[1m'
RESET='\033[0m'

# Fonction pour afficher les messages colorés
print_message() {
    local message="$1"
    local color="${2:-$WHITE}"
    local bold="${3:-false}"
    
    if [ "$bold" = "true" ]; then
        echo -e "${BOLD}${color}${message}${RESET}"
    else
        echo -e "${color}${message}${RESET}"
    fi
}

# Fonction pour afficher l'aide
show_help() {
    print_message "Gestionnaire MySQL en ligne de commande" "$CYAN" "true"
    print_message "Usage: ./mysql_manager.sh [COMMANDE] [OPTIONS]" "$YELLOW"
    echo ""
    
    print_message "COMMANDES:" "$WHITE" "true"
    print_message "  export-all     Exporter toutes les bases de données" "$GREEN"
    print_message "  export-single  Exporter une base spécifique" "$GREEN"
    print_message "  import         Importer une base depuis un fichier SQL" "$GREEN"
    print_message "  list           Lister toutes les bases de données" "$GREEN"
    print_message "  info           Informations sur une base spécifique" "$GREEN"
    print_message "  backup         Créer une sauvegarde complète" "$GREEN"
    print_message "  restore        Restaurer depuis une sauvegarde" "$GREEN"
    print_message "  help           Afficher cette aide" "$GREEN"
    
    echo ""
    print_message "OPTIONS:" "$WHITE" "true"
    print_message "  --database=NAME    Nom de la base de données" "$YELLOW"
    print_message "  --file=PATH        Chemin du fichier SQL" "$YELLOW"
    print_message "  --output=PATH      Chemin de sortie pour l'export" "$YELLOW"
    print_message "  --force            Forcer l'opération sans confirmation" "$YELLOW"
    print_message "  --verbose          Mode verbeux" "$YELLOW"
    
    echo ""
    print_message "EXEMPLES:" "$WHITE" "true"
    print_message "  ./mysql_manager.sh export-all" "$WHITE"
    print_message "  ./mysql_manager.sh export-single --database=cyberchasse" "$WHITE"
    print_message "  ./mysql_manager.sh import --database=newdb --file=backup.sql" "$WHITE"
    print_message "  ./mysql_manager.sh list" "$WHITE"
    print_message "  ./mysql_manager.sh info --database=cyberchasse" "$WHITE"
    print_message "  ./mysql_manager.sh backup" "$WHITE"
    print_message "  ./mysql_manager.sh restore --file=backup.sql" "$WHITE"
}

# Vérification de PHP
check_php() {
    if ! command -v php &> /dev/null; then
        print_message "Erreur: PHP n'est pas installé ou n'est pas dans le PATH" "$RED"
        exit 1
    fi
    
    # Vérifier la version PHP
    PHP_VERSION=$(php -r "echo PHP_VERSION;" 2>/dev/null)
    if [ $? -ne 0 ]; then
        print_message "Erreur: Impossible de déterminer la version de PHP" "$RED"
        exit 1
    fi
    
    print_message "PHP détecté: $PHP_VERSION" "$GREEN"
}

# Vérification du script PHP
check_script() {
    local script_path="mysql_cli_manager.php"
    if [ ! -f "$script_path" ]; then
        print_message "Erreur: Le script $script_path n'existe pas" "$RED"
        exit 1
    fi
    
    if [ ! -r "$script_path" ]; then
        print_message "Erreur: Le script $script_path n'est pas lisible" "$RED"
        exit 1
    fi
}

# Fonction principale
main() {
    local command="$1"
    
    # Vérifications préliminaires
    check_php
    check_script
    
    # Si pas de commande, afficher l'aide
    if [ -z "$command" ]; then
        show_help
        exit 0
    fi
    
    # Si c'est la commande help
    if [ "$command" = "help" ]; then
        show_help
        exit 0
    fi
    
    # Construire la commande PHP
    local php_cmd="php mysql_cli_manager.php"
    
    # Ajouter la commande
    php_cmd="$php_cmd $command"
    
    # Ajouter les options
    shift
    for arg in "$@"; do
        php_cmd="$php_cmd '$arg'"
    done
    
    # Exécuter la commande PHP
    print_message "Exécution: $php_cmd" "$CYAN"
    echo ""
    
    eval $php_cmd
}

# Exécution du script principal
main "$@"
