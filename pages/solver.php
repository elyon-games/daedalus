<!DOCTYPE html>
<html>
<head>
    <title>FastCGI Example</title>
</head>
<body>
    <h1>FastCGI Example</h1>
    <form>
    <form action="/votre-action" method="post">
    <label for="difficulty">Choisissez une difficulté :</label>
    <select id="difficulty" name="difficulty">
        <option value="1">1 - Très facile</option>
        <option value="2">2</option>
        <option value="3">3</option>
        <option value="4">4</option>
        <option value="5">5 - Moyen</option>
        <option value="6">6</option>
        <option value="7">7</option>
        <option value="8">8</option>
        <option value="9">9</option>
        <option value="10">10 - Très difficile</option>
    </select>
    <button type="submit">Envoyer la requête à FastCGI</button>
</form>

    </form>
    <div id="response"></div>
    <script>
        let method = "SOLVERA";
        let donnees = "";

        function withQuery(url, params) {
            if (url.startsWith("/")) {
                const baseUrl = window.location.origin;
                url = `${baseUrl}${url}`;
            }

            const urlObj = new URL(url);
            const queryParams = new URLSearchParams(urlObj.search);

            for (const key in params) {
                if (params[key] === null || params[key] === undefined) {
                    queryParams.delete(key);
                } else {
                    queryParams.set(key, params[key]);
                }
            }

            urlObj.search = queryParams.toString();

            return urlObj.toString();
        }

        document.querySelector('form').addEventListener('submit', function(event) {
            event.preventDefault();
            donnees = document.getElementById('difficulty').value;
            const query = {
                "sa": donnees
            };
            if(donnees == 1){ 
                method = "SOLVERA"; 

            }
            if(donnees == 2){ 
                method = "SOLVERB"; 
                
            }
            if(donnees == 3){ 
                method = "SOLVERC"; 

            }
            if(donnees == 4){ 
                method = "SOLVERD"; 
                
            }
            if(donnees == 5){ 
                method = "SOLVERE"; 

            }
            if(donnees == 6){ 
                method = "SOLVERF"; 
                
            }
            if(donnees == 7){ 
                method = "SOLVERG"; 

            }
            if(donnees == 8){ 
                method = "SOLVERH"; 
                
            }
            if(donnees == 9){ 
                method = "SOLVERI"; 

            }
            if(donnees == 10){ 
                method = "SOLVERJ"; 
                
            }
            
           

           
            fetch(withQuery("/api", query), {
                method,
                body: JSON.stringify(donnees)
            })
            .then(response => response.text())
            .then(data => document.getElementById('response').innerHTML = data);
        });
    </script>
</body>
</html>