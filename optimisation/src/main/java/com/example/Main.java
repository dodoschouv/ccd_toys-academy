package com.example;

import com.sun.net.httpserver.HttpServer;

import java.io.BufferedReader;
import java.io.File;
import java.io.FileReader;
import java.io.IOException;
import java.io.OutputStream;
import java.io.PrintWriter;
import java.net.InetSocketAddress;
import java.nio.charset.StandardCharsets;
import java.util.ArrayList;
import java.util.List;

public class Main {
    private static List<Article> catalogue = new ArrayList<>();
    private static List<Abonne> abonnes = new ArrayList<>();
    private static int maxPoids = 0;
    private static final int[] POINTS_PREFERENCE = {10, 8, 6, 4, 2, 1};

    public static void main(String[] args) throws IOException {
        if (args.length == 0) {
            demarrerServeur();
            return;
        }
        String cheminFichier = args.length >= 1 ? args[0] : "exemple.csv";
        String sortie = args.length >= 2 ? args[1] : "sortie_composition.csv";
        chargerDonnees(cheminFichier);

        List<Box> resultat = optimiser();
        int scoreFinal = calculerScoreTotal(resultat);

        System.out.println("Chargement terminé :");
        System.out.println("- Articles : " + catalogue.size());
        System.out.println("- Abonnés : " + abonnes.size());
        System.out.println("- Poids max : " + maxPoids + "g");
        System.out.println("----------------------------");
        System.out.println("SCORE DE LA COMPOSITION : " + scoreFinal);

        sauverResultat(sortie, scoreFinal, resultat);
    }

    private static void demarrerServeur() throws IOException {
        HttpServer server = HttpServer.create(new InetSocketAddress(80), 0);
        server.createContext("/api/health", exchange -> {
            if (!"GET".equals(exchange.getRequestMethod())) {
                exchange.sendResponseHeaders(405, -1);
                return;
            }
            String body = "{\"status\":\"ok\",\"service\":\"optimization\"}";
            exchange.getResponseHeaders().set("Content-Type", "application/json");
            exchange.sendResponseHeaders(200, body.getBytes(StandardCharsets.UTF_8).length);
            try (OutputStream os = exchange.getResponseBody()) {
                os.write(body.getBytes(StandardCharsets.UTF_8));
            }
        });
        server.setExecutor(null);
        server.start();
        System.out.println("Serveur optimisation démarré sur le port 80 (GET /api/health)");
    }

    public static void chargerDonnees(String fileName) {
        try (BufferedReader br = new BufferedReader(new FileReader(fileName))) {
            String line;
            String mode = "";

            while ((line = br.readLine()) != null) {
                line = line.trim();
                if (line.isEmpty()) continue;

                if (line.toLowerCase().contains("articles")) {
                    mode = "ARTICLES";
                    continue;
                } else if (line.toLowerCase().contains("abonnes")) {
                    mode = "ABONNES";
                    continue;
                } else if (line.toLowerCase().contains("parametres")) {
                    mode = "PARAMETRES";
                    continue;
                }

                String[] columns = line.split(";");

                switch (mode) {
                    case "ARTICLES":
                        Article art = new Article(
                            columns[0], columns[1],
                            Categorie.valueOf(columns[2].trim()),
                            TrancheAge.valueOf(columns[3].trim()),
                            Etat.valueOf(columns[4].trim()),
                            Integer.parseInt(columns[5].trim()),
                            Integer.parseInt(columns[6].trim())
                        );
                        catalogue.add(art);
                        break;

                    case "ABONNES":
                        List<Categorie> prefs = new ArrayList<>();
                        for (String cat : columns[3].split(",")) {
                            prefs.add(Categorie.valueOf(cat.trim()));
                        }
                        Abonne ab = new Abonne(columns[0], columns[1], 
                                               TrancheAge.valueOf(columns[2].trim()), prefs);
                        abonnes.add(ab);
                        break;

                    case "PARAMETRES":
                        maxPoids = Integer.parseInt(columns[0].trim());
                        break;
                }
            }
        } catch (IOException e) {
            e.printStackTrace();
        }
    }
    public static int calculerPointsArticle(Abonne abonne, Article article, int nbDejaPresentsMemeCategorie) {
        int rangBase = abonne.getPreferences().indexOf(article.getCategorie());
        int rangFinal = rangBase + nbDejaPresentsMemeCategorie;

        int pointsPref;
        if (rangFinal < POINTS_PREFERENCE.length) {
            pointsPref = POINTS_PREFERENCE[rangFinal];
        } else {
            pointsPref = 1; 
        }
        int bonusEtat = 0;
        switch (article.getEtat()) {
            case N:  bonusEtat = 2; break; 
            case TB: bonusEtat = 1; break;
            case B:  bonusEtat = 0; break;
        }

        return pointsPref + bonusEtat;
    }

    public static int calculerScoreBox(Box box) {
        int scoreTotalBox = 0;
        java.util.Map<Categorie, Integer> compteurCategories = new java.util.HashMap<>();

        for (Article art : box.getArticles()) {
            int nbDejaPresents = compteurCategories.getOrDefault(art.getCategorie(), 0);
            scoreTotalBox += calculerPointsArticle(box.getAbonne(), art, nbDejaPresents);
            compteurCategories.put(art.getCategorie(), nbDejaPresents + 1);
        }
        
        return scoreTotalBox;
    }

    public static int calculerScoreTotal(List<Box> toutesLesBox) {
        int scoreGlobal = 0;
        List<Integer> nbArticlesParAbonne = new ArrayList<>();

        for (Box box : toutesLesBox) {
            if (box.getArticles().isEmpty()) {
                scoreGlobal -= 10;
            } else {
                scoreGlobal += calculerScoreBox(box);
            }
            nbArticlesParAbonne.add(box.getArticles().size());
        }

        int malusEquite = 0;
        for (int i = 0; i < nbArticlesParAbonne.size(); i++) {
            boolean estConcerne = false;
            for (int j = 0; j < nbArticlesParAbonne.size(); j++) {
                if (i != j && Math.abs(nbArticlesParAbonne.get(i) - nbArticlesParAbonne.get(j)) >= 2) {
                    estConcerne = true;
                    break;
                }
            }
            if (estConcerne) {
                malusEquite -= 10;
            }
        }
        
        return scoreGlobal + malusEquite;
    }

    public static List<Box> optimiser() {
        List<Box> boxes = new ArrayList<>();
        for (Abonne ab : abonnes) {
            boxes.add(new Box(ab));
        }

        for (Article art : catalogue) {
            Box meilleureBox = null;
            int meilleurGain = -1;

            for (Box box : boxes) {
                if (art.getAge() != box.getAbonne().getAgeEnfant()) continue;

                if (box.getPoidsTotal() + art.getPoids() > maxPoids) continue;

                long dejaPresents = box.getArticles().stream()
                    .filter(a -> a.getCategorie() == art.getCategorie())
                    .count();
                
                int gain = calculerPointsArticle(box.getAbonne(), art, (int)dejaPresents);

                if (gain > meilleurGain) {
                    meilleurGain = gain;
                    meilleureBox = box;
                }
            }
            if (meilleureBox != null) {
                meilleureBox.ajouterArticle(art, maxPoids);
            }
        }
        return boxes;
    }

    public static void sauverResultat(String fileName, int score, List<Box> boxes) {
        try (PrintWriter writer = new PrintWriter(new File(fileName))) {
            writer.println(score);

            for (Box box : boxes) {
                String prenom = box.getAbonne().getPrenom();
                for (Article art : box.getArticles()) {

                    writer.println(String.format("%s;%s;%s;%s;%s",
                        prenom,
                        art.getId(),
                        art.getCategorie(),
                        art.getAge(),
                        art.getEtat()
                    ));
                }
            }
            System.out.println("Fichier de sortie généré : " + fileName);
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    
}