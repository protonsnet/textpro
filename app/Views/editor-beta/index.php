<style>
    html, body { height: 100% !important; margin: 0 !important; padding: 0 !important; overflow: hidden !important; }
    #placeholder { height: 100vh !important; width: 100vw !important; }
    #loading {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        display: flex; justify-content: center; align-items: center;
        background: #ffffff; z-index: 9999; font-family: sans-serif;
    }
</style>

<div id="loading">Carregando Editor OnlyOffice...</div>

<div id="placeholder"></div>

<!--<script type="text/javascript" src="http://192.168.0.102:8081/web-apps/apps/api/documents/api.js"></script>-->
<!-- Produção -->
 <script type="text/javascript" src="https://office.moveflexi.com/web-apps/apps/api/documents/api.js"></script> 

<script>
    function initEditor() {
        if (typeof DocsAPI === 'undefined') {
            document.getElementById('loading').innerHTML = "Erro: API do OnlyOffice não encontrada.";
            return;
        }

        // Pegamos a config enviada pelo PHP
        const config = <?= $config ?>;

        // Adicionamos o evento de "Pronto" diretamente na configuração
        // Esta é a forma mais segura de evitar o erro .attachEvent
        config.events = {
            "onAppReady": function() {
                console.log("OnlyOffice está pronto!");
                document.getElementById('loading').style.display = 'none';
            },
            "onError": function(event) {
                console.error("Erro no OnlyOffice:", event);
                alert("Erro ao abrir o documento. Verifique o console.");
            }
        };

        try {
            // Inicializa o editor
            new DocsAPI.DocEditor("placeholder", config);
        } catch (e) {
            console.error("Falha na inicialização:", e);
            document.getElementById('loading').innerHTML = "Erro ao iniciar o editor.";
        }
    }

    // Aguarda o carregamento da janela para iniciar
    window.onload = initEditor;
    
</script>