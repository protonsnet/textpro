<?php

/**
 * Ponto de entrada do site.
 *
 * Este script simples redireciona todas as requisições
 * para a pasta 'public', que contém os arquivos acessíveis
 * via navegador. Isso é uma prática de segurança que
 * impede o acesso direto a arquivos sensíveis da aplicação.
 */

// Redireciona para a pasta 'public'
header('Location: public/');
exit; // Garante que o script pare de ser executado após o redirecionamento