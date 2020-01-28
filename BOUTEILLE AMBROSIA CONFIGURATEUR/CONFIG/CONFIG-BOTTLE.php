<?php
include_once ('config.php');
$bdd = getbdd();
$produits = getAllProduit($bdd);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Configurateur</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <link type="text/css" rel="stylesheet" href="main.css">
    <link rel="icon" type="image/jpg" href="textures/agarta.jpg"/>
    <script defer src="https://use.fontawesome.com/releases/v5.0.7/js/all.js"></script>
    <script>
        function openSlideMenu(){
            document.getElementById('menu').style.width = '250px';
            document.getElementById('content').style.marginLeft = '250px';
            document.getElementById('hamburger-img').style.display = "none"
        }
        function closeSlideMenu(){
            for (let j = 0; j < 5 ; j++) {
                if (j===0){
                    j=""
                }
                document.getElementById('content-ui'+j).style.transition= '';
                document.getElementById('content-ui'+j).style.overflow= '';
                document.getElementById('content-ui'+j).style.maxHeight = '';
            }
            document.getElementById('menu').style.width = '0';
            document.getElementById('content').style.marginLeft = '0';
            document.getElementById('hamburger-img').style.display = ""
        }
    </script>
    <script src="ccapture/build/CCapture.all.min.js"></script>
</head>

<body>
<div class="parent">

    <div id="info">
        <a id="checkbox"><input id="checkboxInput" type="checkbox" name=checkbox checked> Affichage</a>
        <a><img id="info-image" src="miniatures/info.png"></a>
        <a style="display: none" id="info-txt"></a>

        <div class="grille" id="grille"></div>

    </div>
</div>

<div id="content">

    <span class="slide">
      <a href="#" id="hamburger-img" onclick="openSlideMenu()">
        <i class="fas fa-bars"></i>
      </a>
    </span>

    <div id="menu" class="nav">
        <a href="#" class="close" onclick="closeSlideMenu()">
            <i class="fas fa-times"></i>
        </a>

        <div id="sidebar">
            <div id="content-sidebar">
                <div id="new-ui">
                    <div id="header-ui">
                        <span>Choisir un produit : </span>
                    </div>
                    <div id="content-ui">
                        <ul id="ul-list">
                            <?php
                            $a = -1;
                            while ($produit = $produits->fetch_assoc()) {
                                $a+=1;
                                ?>
                                <li id="<?= 'model'.$a ?>"><?= $produit['nom']?></li>
                                <?php
                            };
                            ?>
                        </ul>
                    </div>
                </div>
                <div id="new-ui">
                    <div id="header-ui1">
                        <span>Choisir un design : </span>
                    </div>
                    <div id="content-ui1">
                        <span><select class="select-css" id="body-mat"></select></span>
                        <br>
                        <span>Voici la liste des disigns disponibles pour le moment</span>
                    </div>
                </div>
                <div id="new-ui">
                    <div id="header-ui2">
                        <span>Background : </span>
                    </div>
                    <div id="content-ui2">
                        <ul id="ul-list">
                            <li id="fondGris">Fond gris</li>
                            <li id="fondNature">Fond Nature</li>
                        </ul>
                    </div>
                </div>
                <div id="new-ui">
                    <div id="header-ui3">
                        <span>Sol : </span>
                    </div>
                    <div id="content-ui3">
                        <span><img id="img-rgb" src="textures/sol.jpeg" width="25px" height="15px">Texture : <input id="input-parcourir" type="file"></span>
                        <br>
                        <span><img id="img-rgb" src="miniatures/rgb.jpg" width="25px" height="15px">Palette de couleurs: <br><input id="input-color" type="color"></span>
                        <br>
                        <span>Activer : <input id="checkbox-sol" type="checkbox" checked></span>
                        <br>
                    </div>
                </div>
                <div id="new-ui">
                    <div id="header-ui4">
                        <span>Options : </span>
                    </div>
                    <div id="content-ui4">
                        <ul id="ul-list">
                            <li id="auto-rotate">
                                <img src="miniatures/rotate.png" height="25px">Rotation 360°
                            </li>
                            <li>
                                <a href="addTexture.php"> <input id="button-usdz" type="button" value="Ajouter une texture"> </a>
                            </li>
                            <li>
                                <button id="button-usdz">Fichiers Usdz</button>
                            </li>
                            <li>
                                <button id="reinitialiser">Réinitialiser</button>
                            </li>
                            <li>
                                <button id="screenshot">ScreenShot</button>
                            </li>
                            <div class="buttons">
                                <button id="start">Start recording to WebM</button>
                                <button id="stop">Stop (or wait 4 seconds)</button>
                            </div>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="container">

</div>


<script type="module">

    import * as THREE from '../build/three.module.js';
    import Stats from './jsm/libs/stats.module.js';
    import {GLTFLoader} from './jsm/loaders/GLTFLoader.js';
    import {DRACOLoader} from './jsm/loaders/DRACOLoader.js';
    import {OrbitControls} from './jsm/controls/OrbitControls.js';
    import {PMREMGenerator} from './jsm/pmrem/PMREMGenerator.js';
    import {PMREMCubeUVPacker} from './jsm/pmrem/PMREMCubeUVPacker.js';
    import {EquirectangularToCubeGenerator} from './jsm/loaders/EquirectangularToCubeGenerator.js';
    import {RGBELoader} from './jsm/loaders/RGBELoader.js';
    ////INITIALISATION DES VARIABLES////
    let scnCamera, scene, mesh, meshFloor, renderer, objModel, materialsLib, envMap, controls;

    const recorder = new CCapture({
        verbose: false,
        display: true,
        framerate: 60,
        quality: 100,
        format: 'webm',
        timeLimit: 23,
        frameLimit: 0,
        autoSaveTime: 0
    });

    let bodyMatSelect = document.getElementById('body-mat');
    let bodyModelSelect = document.getElementById('body-models');
    let grilleSelect = document.getElementById('grille');
    let rotate = document.getElementById('auto-rotate');
    let info = document.getElementById('info-image');
    let infoTxt = document.getElementById('info-txt');
    let buttonUsdz = document.getElementById('button-usdz');
    let inter = document.getElementById('inter');
    let checkboxAffichage = document.querySelector("input[name=checkbox]");
    let sidebare = document.getElementById('sidebar');
    let headerUi = document.getElementById('header-ui');
    let headerUi1 = document.getElementById('header-ui1');
    let headerUi2 = document.getElementById('header-ui2');
    let headerUi3 = document.getElementById('header-ui3');
    let headerUi4 = document.getElementById('header-ui4');
    let contentUi = document.getElementById('content-ui');
    let contentUi1 = document.getElementById('content-ui1');
    let contentUi2 = document.getElementById('content-ui2');
    let contentUi3 = document.getElementById('content-ui3');
    let contentUi4 = document.getElementById('content-ui4');
    let eLiquide = document.getElementById('eliquide');
    let model = document.getElementById('model0');
    let model1 = document.getElementById('model1');
    let fondGris = document.getElementById('fondGris');
    let fondNature = document.getElementById('fondNature');
    let imageParcourir = document.getElementById('input-parcourir');
    let inputColor = document.getElementById('input-color');
    let checkboxSol = document.getElementById('checkbox-sol');
    let reinitialiser = document.getElementById('reinitialiser');
    let hamburgerImg = document.getElementById('hamburger-img');
    let screenshot = document.getElementById('screenshot');


    let liens;

    let n, i, t, x, a, b, c, d, e = 0;
    let fond,sol = 0;

    let objetActuel = "E-Liquide";

    let modelParts = {
        body: [],
    };

    let scnCameraTarget = new THREE.Vector3();
    ////INITIALISATION DES VARIABLES FIN////

    ////DEBUT addEventListener() List////
    model.addEventListener('click',updateModelELiquide);
    model1.addEventListener('click',updateModelCarte);
    bodyMatSelect.addEventListener('change', updateMaterials);
    rotate.addEventListener("click", OnOffRotation);
    info.addEventListener('click', OnOffInfo);
    buttonUsdz.addEventListener('click', OnOffUsdz);
    checkboxAffichage.addEventListener('change', checkboxAfficher);
    checkboxSol.addEventListener('change',function () {
        sol = 0;
        changerSol()
    });
    headerUi.addEventListener('click', OnOffHeaderUi);
    headerUi1.addEventListener('click', OnOffHeaderUi1);
    headerUi2.addEventListener('click', OnOffHeaderUi2);
    headerUi3.addEventListener('click', OnOffHeaderUi3);
    headerUi4.addEventListener('click', OnOffHeaderUi4);
    reinitialiser.addEventListener('click',reinitialise);
    fondGris.addEventListener("click", function () {
        fond = 0;
        changerFond()
    });
    fondNature.addEventListener('click', function () {
        fond = 1;
        changerFond()
    });
    imageParcourir.addEventListener('change',function () {
        sol = 0;
        changerSol()
    });
    inputColor.addEventListener('change',function () {
        sol = 1;
        changerSol()
    });
    screenshot.addEventListener('click',takeScreenshot);
    ////FIN addEventListener() List////

    ////INITIALISATION////
    function init() {
        let container = document.getElementById('container');

        scene = new THREE.Scene();

        scnCamera = new THREE.PerspectiveCamera(40, window.innerWidth / window.innerHeight, 0.001, 100);
        scnCamera.position.set(30, 5, 0);

        let urls = ['px.jpg', 'nx.jpg', 'py.jpg', 'ny.jpg', 'pz.jpg', 'nz.jpg'];
        let loader = new THREE.CubeTextureLoader().setPath('textures/cube/studio/');

        loader.load(urls, function (texture) {

            let helper = new THREE.CameraHelper(scnCamera);
            scene.add(helper);

            ////ORBIT CONTROLS////
            let controls = new OrbitControls(scnCamera, renderer.domElement);

            controls.enablePan = false;
            controls.enableZoom = true;
            controls.minDistance = 1;
            controls.maxDistance = 4;
            controls.enableDamping = true;
            controls.dampingFactor = 0.2;
            controls.minPolarAngle = 0.1; //Uper
            controls.maxPolarAngle = 1.8; //Lowers
            controls.target.set(0, 0.8, 0);
            controls.autoRotate = true;
            controls.autoRotateSpeed = 0.05;
            controls.update();
            ////ORBIT CONTROLS FIN////

            //SKYBOX////
            scene = new THREE.Scene();
            scene.background = new THREE.TextureLoader().load("textures/gris.jpg"); // (Ciel)
            scene.backgroundSphere = true;
            ////SKYBOX FIN////

            ////SOL////
            meshFloor = new THREE.Mesh(
                new THREE.CircleGeometry(1, 50),
                new THREE.MeshStandardMaterial({
                    map: new THREE.TextureLoader().load("textures/marbre.jpg"),
                    roughness: 1.0
                }));
            meshFloor.rotation.x -= Math.PI / 2;
            meshFloor.receiveShadow = true;
            scene.add(meshFloor);
            ////FIN SOL////

            ////LIGHT////
            let light = new THREE.HemisphereLight(0xffffff, 0x444444);
            light.position.set(0, 10, 0);
            scene.add(light);

            ////AMBIENT LIGHT////
            let ambient = new THREE.AmbientLight(0xffffff, 1.5);
            scene.add(ambient);
            ////AMBIENT LIGHT FIN////

            ////LIGHT 1 LUMIERE ROUGE////
            var SpotLight = new THREE.SpotLight(0xffffff, 3);
            SpotLight.position.set(3, 1, 0);
            SpotLight.target.position.set(0, 0, 0);
            SpotLight.castShadow = true;
            SpotLight.shadowCameraVisible = false;
            SpotLight.decay = 1.5;
            SpotLight.penumbra = 1;
            SpotLight.angle = 0.4;

            scene.add(SpotLight);

            // var spotLightHelper = new THREE.SpotLightHelper( SpotLight, 0xFF0000 ) ;
            // scene.add( spotLightHelper );

            //// //LIGHT 2 LUMIERE VERTE////
            var SpotLight = new THREE.SpotLight(0xffffff, 5);
            SpotLight.position.set(3, 1, 2);
            SpotLight.target.position.set(0, 0, 0);
            SpotLight.castShadow = true;
            SpotLight.decay = 1.5;
            SpotLight.penumbra = 1;
            SpotLight.angle = 0.4;

            scene.add(SpotLight);

            // var spotLightHelper = new THREE.SpotLightHelper( SpotLight, 0x64FF00 ) ;
            // scene.add( spotLightHelper );

            ////LIGHT 3 LUMIERE BLEU////
            var SpotLight = new THREE.SpotLight(0xffffff, 2);
            SpotLight.position.set(-3, 1, 1);
            SpotLight.target.position.set(0, 0, 0);
            SpotLight.castShadow = true;
            SpotLight.decay = 1.5;
            SpotLight.penumbra = 1;
            SpotLight.angle = 0.4;

            scene.add(SpotLight);

            // var spotLightHelper = new THREE.SpotLightHelper( SpotLight, 0x00C9FF ) ;
            // scene.add( spotLightHelper );

            ////LIGHT 4 LUMIERE JAUNE////

            // var SpotLight = new THREE.SpotLight(0xffffff, 5);
            // SpotLight.position.set(-5, 5, -15);
            // SpotLight.target.position.set(0, 0, 0);
            // SpotLight.castShadow = true;
            // SpotLight.decay = 1;
            // SpotLight.penumbra = 1;
            // scene.add(SpotLight);

            // var spotLightHelper = new THREE.SpotLightHelper( SpotLight, 0xFFFF00 ) ;
            // scene.add( spotLightHelper );

            ////////LIGHT FIN////

            let pmremGenerator = new PMREMGenerator(texture);
            pmremGenerator.update(renderer);


            let pmremCubeUVPacker = new PMREMCubeUVPacker(pmremGenerator.cubeLods);
            pmremCubeUVPacker.update(renderer);

            envMap = pmremCubeUVPacker.CubeUVRenderTarget.texture;

            pmremGenerator.dispose();
            pmremCubeUVPacker.dispose();

            //
            initModel();
            initMaterials();
            initMaterialSelectionMenus();

        });

        ////RENDER ENGINE ////
        renderer = new THREE.WebGLRenderer({antialias: true});
        renderer = new THREE.WebGLRenderer({alpha: true});
        renderer.setPixelRatio(window.devicePixelRatio);
        renderer.setSize(window.innerWidth, window.innerHeight);
        renderer.gammaOutput = true;
        renderer.physicallyCorrectLights = true;
        renderer.gammaInput = true;
        renderer.shadowMap.type = THREE.PCFSoftShadowMap;
        renderer.setClearColor(0x000000, 1);
        renderer.shadowMap.enabled = true;
        container.appendChild(renderer.domElement);
        ////RENDER ENGINE FIN////

        window.addEventListener('resize', onWindowResize, false);

        renderer.setAnimationLoop(function () {
            update();
            render()
        });
    }
    ////INITIALISATION FIN////

    ////REFRESH PAGE////
    function reinitialise(){
        window.location.reload()
    }
    ////REFRESH PAGE FIN////

    ////CHANGER BACKGROUND////
    function changerFond() {
        if (fond === 0) {
            scene.remove(mesh);
            scene.background = new THREE.TextureLoader().load("textures/gris.jpg"); // (Ciel)
            scene.backgroundSphere = true;
        } else if (fond === 1) {
            let path = "textures/ocean/";
            let format = '.jpg';
            let urls = [
                path + 'right' + format,
                path + 'left' + format,
                path + 'top' + format,
                path + 'bottom' + format,
                path + 'back' + format,
                path + 'front' + format
            ];

            let reflectionCube = THREE.ImageUtils.loadTextureCube(urls);
            reflectionCube.format = THREE.RGBFormat;

            let shader = THREE.ShaderLib["cube"];
            shader.uniforms["tCube"].value = reflectionCube;

            let material = new THREE.ShaderMaterial({
                fragmentShader: shader.fragmentShader,
                vertexShader: shader.vertexShader,
                uniforms: shader.uniforms,
                depthWrite: false,
                side: THREE.BackSide
            });

            mesh = new THREE.Mesh(new THREE.BoxGeometry(100, 100, 100), material);
            scene.add(mesh);
        }
    }
    ////CHANGER BACKGROUND FIN////

    ////CHANGER SOL////
    function changerSol() {
        if (checkboxSol.checked) {
            if (sol === 0) {
                scene.remove(meshFloor);
                if (imageParcourir.files[0]) {
                    meshFloor = new THREE.Mesh(
                        new THREE.CircleGeometry(1, 50),
                        new THREE.MeshStandardMaterial({
                            map: new THREE.TextureLoader().load('image/' + imageParcourir.files[0].name),
                            roughness: 1.0
                        }));
                } else {
                    meshFloor = new THREE.Mesh(
                        new THREE.CircleGeometry(1, 50),
                        new THREE.MeshStandardMaterial({
                            map: new THREE.TextureLoader().load('image/marbre.jpg'),
                            roughness: 1.0
                        }));
                }
                meshFloor.rotation.x -= Math.PI / 2;
                meshFloor.receiveShadow = true;
                scene.add(meshFloor);
            } else if (sol === 1) {
                scene.remove(meshFloor);
                meshFloor = new THREE.Mesh(
                    new THREE.CircleGeometry(1, 50),
                    new THREE.MeshStandardMaterial({color: inputColor.value, roughness: 0.6}));
                meshFloor.rotation.x -= Math.PI / 2;
                meshFloor.receiveShadow = true;
                scene.add(meshFloor);
            }
        } else {
            scene.remove(meshFloor);
        }

    }
    ////CHANGER SOL FIN////

    ////INITIALISATION MODEL////
    function initModel() {
        let dracoLoader = new DRACOLoader();
        dracoLoader.setDecoderPath('js/libs/draco/gltf/');

        let loader = new GLTFLoader();
        loader.setDRACOLoader(dracoLoader);

        loader.load('models/gltf/ambrosia/bouteille-ambrosia.gltf', function (gltf) {

            objModel = gltf.scene.children[0];

            objModel.traverse(function (child) {
                if (child.isMesh) {
                    child.material.envMap = envMap;
                }
            });

            scene.add(objModel);

            // car parts for material selection
            modelParts.body.push(objModel.getObjectByName('body'));

            updateMaterials();
        });
    }
    ////INITIALISATION MODEL FIN////

    ////UPDATE MODEL LIQUIDE////
    function updateModelELiquide() {
        objetActuel = "E-Liquide";
        scene.remove(objModel);
        let dracoLoader = new DRACOLoader();
        dracoLoader.setDecoderPath('js/libs/draco/gltf/');

        let loader = new GLTFLoader();
        loader.setDRACOLoader(dracoLoader);

        liens = "models/gltf/ambrosia/bouteille-ambrosia.gltf";


        loader.load(liens, function (gltf) {
            objModel = gltf.scene.children[0];

            objModel.traverse(function (child) {
                child.type = "Mesh";
                if (child.isMesh) {
                    child.material.envMap = envMap;
                }
            });

            scene.add(objModel);

            // car parts for material selection
            if (objModel.getObjectByName('body')) {
                modelParts.body.push(objModel.getObjectByName('body'));
            }
            initMaterialSelectionMenus();
            updateMaterials();
        });
    }
    ////UPDATE MODEL E-LIQUIDE FIN////

    ////UPDATE MODEL CARTE////
    function updateModelCarte() {
        objetActuel = "Carte de visite";
        scene.remove(objModel);
        let dracoLoader = new DRACOLoader();
        dracoLoader.setDecoderPath('js/libs/draco/gltf/');

        let loader = new GLTFLoader();
        loader.setDRACOLoader(dracoLoader);

        liens = "models/gltf/carte/carte.gltf";

        loader.load(liens, function (gltf) {
            objModel = gltf.scene.children[0];

            objModel.traverse(function (child) {
                child.type = "Mesh";
                if (child.isMesh) {
                    child.material.envMap = envMap;
                }
            });

            scene.add(objModel);

            // car parts for material selection
            if (objModel.getObjectByName('body')) {
                modelParts.body.push(objModel.getObjectByName('body'));
            }
            initMaterialSelectionMenus();
            updateMaterials();

        });
    }
    ////UPDATE MODEL CARTE FIN////

    ////INITIALISATION MATERIALS////
    function initMaterials() {
        materialsLib = {
            main: [
                <?php
                $produits = getAllProduit($bdd);
                $etiquettes = getAllEtiquette($bdd);
                $cartes = getAllCartes($bdd);
                if ($etiquettes){
                    while ($etiquette = $etiquettes->fetch_assoc()){
                ?>
                new THREE.MeshStandardMaterial(
                    {
                        map: new THREE.TextureLoader().load("<?=$etiquette['path_texture'] ?>"),
                        roughnessMap: new THREE.TextureLoader().load("<?=$etiquette['path_rgh'] ?>"),
                        metalness: 0.0,
                        envMap: envMap,
                        envMapIntensity: 0.1,
                        name: "<?=$etiquette['nom_texture']?>"
                    }),
                <?php
                    }
                }
                ?>
            ],
            Carte:[
                <?php
                if ($cartes){
                while ($carte = $cartes->fetch_assoc()){
                ?>
                new THREE.MeshStandardMaterial(
                    {
                        map: new THREE.TextureLoader().load("<?=$carte['path_texture']?>"),
                        roughnessMap: new THREE.TextureLoader().load("<?=$carte['path_rgh']?>"),
                        <?php
                            if (!empty($carte['path_metal'])){
                        ?>
                                metalnessMap: new THREE.TextureLoader().load("<?= $carte['path_metal'] ?>"),
                        <?php
                            }
                        ?>
                        <?php
                            if (!empty($carte['path_normal'])){
                        ?>
                                normalMap: new THREE.TextureLoader().load( "<?=$carte['path_normal'] ?>" ) ,
                        <?php
                            }
                        ?>
                        envMap: envMap,
                        envMapIntensity: 0.1,
                        name: "<?=$carte['nom_texture']?>"
                    }),
                <?php
                }
                }
                ?>
            ],
        };
    }
    ////INITIALISATION MATERIALS FIN////

    ////INITIALISATION MENU MATERIALS////
    function initMaterialSelectionMenus() {

        function addOption(name, menu) {
            let option = document.createElement('option');
            option.text = name;
            option.value = name;
            menu.add(option);
        }

        if (objetActuel === "E-Liquide") {
            bodyMatSelect.innerHTML = "";
            affichageGrille();

            materialsLib.main.forEach(function (material) {
                addOption(material.name, bodyMatSelect);
            });
        } else if (objetActuel === "Carte de visite") {
            bodyMatSelect.innerHTML = "";
            affichageGrille();

            materialsLib.Carte.forEach(function (material) {
                addOption(material.name, bodyMatSelect);
            });
        }
        bodyMatSelect.selectedIndex = 0;
    }
    ////INITIALISATION MENU MATERIALS FIN////

    ////UPDATE MENU MATERIALS////
    function updateMaterials() {
        if (objetActuel === "E-Liquide") {
            let bodyMat = materialsLib.main[bodyMatSelect.selectedIndex];

            modelParts.body.forEach(part => part.material = bodyMat);
        } else if (objetActuel === "Carte de visite") {
            let bodyMatt = materialsLib.Carte[bodyMatSelect.selectedIndex];
            modelParts.body.forEach(part => part.material = bodyMatt);
        }
    }
    ////UPDATE MENU MATERIALS FIN////

    ////CLICK FILES USDZ////
    function OnOffUsdz() {
        t += 1;
        if (t === 1) {
        } else {
            t = 0
        }
        affichageGrille()
    }
    ////CLICK FILES USDZ FIN////

    ////DISPLAY GRID USDZ////
    function affichageGrille() {
        let src = {
            0: 'miniatures/belle-prune.jpg',
            1: 'miniatures/boree.jpg',
            2: 'miniatures/crepe.jpg',
            3: 'miniatures/cupcake.jpg',
            4: 'miniatures/euros.jpg',
            5: 'miniatures/notos.jpg',
            6: 'miniatures/super-beignet.jpg',
            7: 'miniatures/zephyr.jpg'
        };
        let srcCarte = {
            0: 'miniatures/FinitionDoreeSurFondNoir.png',
            1: 'miniatures/FinitionDoreeSurFondBlanc.png',
            2: 'miniatures/FinitionDoreeArgentSurFondNoir.png',
            3: 'miniatures/VernisSelectif.png',
            4: 'miniatures/VernisSelectif3D.png',
            5: 'miniatures/Gauffrage.png'
        };
        grilleSelect.style.display = "";
        grilleSelect.innerHTML = null;
        if (t === 0) {
            if (objetActuel === "E-Liquide") {
                for (let i = 0; i < 8; i++) {
                    grilleSelect.innerHTML += '<a href="usdz/bottle-' + (i + 1) + '.usdz">' + '<img class="miniature" src="' + src[i] + '"></a>'
                }
            } else if (objetActuel === "Carte de visite") {
                for (let i = 0; i < 6; i++) {
                    grilleSelect.innerHTML += '<a href="#">' + '<img class="miniature" src="' + srcCarte[i] + '"></a>'
                }
            }
        } else {
            grilleSelect.style.display = "none";
        }
    }
    ////DISPLAY GRID USDZ FIN////

    ////RESIZE WINDOW////
    function onWindowResize() {
        scnCamera.aspect = window.innerWidth / window.innerHeight;
        scnCamera.updateProjectionMatrix();

        renderer.setSize(window.innerWidth, window.innerHeight);
    }
    ////RESIZE WINDOW FIN////

    ////UPDATE////
    function update() {

    }
    ////UPDATE FIN////

    ////CLICK ROTATION 360////
    function OnOffRotation() {
        n += 1;
        if (n === 1) {
        } else {
            n = 0
        }
        animate()
    }
    ////CLICK ROTATION 360 FIN////

    ////ANIMATION 360////
    function animate() {
        if (n === 0) {
            console.log(objModel.rotation.z)
            objModel.rotation.z += 0.01;
            meshFloor.rotation.z -= 0.01;
            requestAnimationFrame(animate);
            render()
            if (objModel.rotation.z === 4.709245813246191){
                n=1;
                objModel.rotation.z = -1.570754186753754;
                render()
            }
        } else {
            render()
        }
    }
    ////ANIMATION 360 FIN////

    ////RENDER////
    function render() {
        renderer.render(scene, scnCamera);
        recorder.capture(renderer.domElement);

    }
    ////RENDER FIN////

    ////CLICK INFO BUTTON////
    function OnOffInfo() {
        i += 1;
        if (i === 1) {
        } else {
            i = 0;
        }
        infoProduit()
    }
    ////CLICK INFO BUTTON FIN////

    ////INFO TEXT BUTTON////
    function infoProduit() {
        if (i === 0) {
            if (objetActuel === "E-Liquide") {
                infoTxt.style.display = null;
                infoTxt.innerHTML = "Vous pouvez voir quelques exemples d'étiquettes que nous sommes capables de designer pour vous."
            } else if (objetActuel === "Carte de visite") {
                infoTxt.style.display = null;
                infoTxt.innerHTML = "Vous pouvez voir quelques exemples de carte de visite avec plusieurs designs que nous sommes capables de réaliser pour vous "
            }
        } else {
            infoTxt.style.display = "none"
        }
    }
    ////INFO TEXT BUTTON FIN////

    ////CLICK MENU////
    function OnOffMenu() {
        x += 1;
        if (x === 1) {
        } else {
            x = 0
        }
        displayMenu()
    }
    ////CLICK MENU FIN////

    ////DISPLAY MENU////
    function displayMenu() {
        if (x === 0) {
            sidebare.style.display = "none"
        } else {
            sidebare.style.display = ""
        }
    }
    ////DISPLAY MENU FIN////

    ////CHECKBOX AFFICHAGE////
    function checkboxAfficher() {
        if (checkboxAffichage.checked) {
            grilleSelect.style.display = null;
            info.style.display = null;
            if (infoTxt.style.display !== "none") {
                infoTxt.style.display = null;
            }
            hamburgerImg.style.display = null
        } else {
            grilleSelect.style.display = "none";
            info.style.display = "none";
            infoTxt.style.display = "none";
            hamburgerImg.style.display = "none"
        }
    }
    ////CHECKBOX AFFICHAGE FIN////

    ////OnOff HEADER UI////
    function OnOffHeaderUi() {
        a += 1;
        if (a === 1) {
        } else {
            a = 0
        }
        displayUi()
    }

    function OnOffHeaderUi1() {
        b += 1;
        if (b === 1) {
        } else {
            b = 0
        }
        displayUi1()
    }

    function OnOffHeaderUi2() {
        c += 1;
        if (c === 1) {
        } else {
            c = 0
        }
        displayUi2()
    }

    function OnOffHeaderUi3() {
        d += 1;
        if (d === 1) {
        } else {
            d = 0
        }
        displayUi3()
    }

    function OnOffHeaderUi4() {
        e += 1;
        if (e === 1) {
        } else {
            e = 0
        }
        displayUi4()
    }
    ////OnOff HEADER UI FIN////

    ////DISPLAY UI////
    function displayUi() {
        if (a === 1) {
            contentUi.style.transition= '';
            contentUi.style.overflow= '';
            contentUi.style.maxHeight = '';
        } else {
            contentUi.style.transition= 'max-height 0.50s ease-out';
            contentUi.style.overflow= 'visible';
            contentUi.style.maxHeight = '200px';
        }
    }
    ////DISPLAY UI FIN////

    ////DISPLAY UI 1////
    function displayUi1() {
        if (b === 1) {
            contentUi1.style.transition= '';
            contentUi1.style.overflow= '';
            contentUi1.style.maxHeight = '';
        } else {
            contentUi1.style.transition= 'max-height 0.50s ease-out';
            contentUi1.style.overflow= 'visible';
            contentUi1.style.maxHeight = '200px';
        }
    }
    ////DISPLAY UI FIN////

    ////DISPLAY UI 2////
    function displayUi2() {
        if (c === 1) {
            contentUi2.style.transition= '';
            contentUi2.style.overflow= '';
            contentUi2.style.maxHeight = '';
        } else {
            contentUi2.style.transition= 'max-height 0.50s ease-out';
            contentUi2.style.overflow= 'visible';
            contentUi2.style.maxHeight = '200px';
        }
    }
    ////DISPLAY UI FIN////

    ////DISPLAY UI 3////
    function displayUi3() {
        if (d === 1) {
            contentUi3.style.transition= '';
            contentUi3.style.overflow= '';
            contentUi3.style.maxHeight = '';
        } else {
            contentUi3.style.transition= 'max-height 0.50s ease-out';
            contentUi3.style.overflow= 'visible';
            contentUi3.style.maxHeight = '200px';
        }
    }
    ////DISPLAY UI FIN////

    ////DISPLAY UI 3////
    function displayUi4() {
        if (e === 0) {
            contentUi4.style.transition= '';
            contentUi4.style.overflow= '';
            contentUi4.style.maxHeight = '';
        } else {
            contentUi4.style.transition= 'max-height 0.50s ease-out';
            contentUi4.style.overflow= 'visible';
            contentUi4.style.maxHeight = '200px';
        }
    }
    ////DISPLAY UI FIN////

    function takeScreenshot() {
        let width = "1080";
        let height = "920";
        scnCamera.aspect = width / height;
        scnCamera.updateProjectionMatrix();
        renderer.setSize(  width, height );

        renderer.render( scene, scnCamera, null, false );

        const dataURL = renderer.domElement.toDataURL( 'image/jpg' );

        // Marche uniquement pour les navigateurs autres que Edge et IE //
            if (window.navigator.msSaveBlob){
                alert("Pas disponible sur IE/Edge, utilisez un autre navigateur")
            }else{
                const a = document.createElement("a");

                document.body.appendChild(a);
                a.href = dataURL;
                a.download = "screenshot.jpg";
                a.click();
                document.body.removeChild(a);
            }
        // FIN//

        // reset to old dimensions (cheat version)
        onWindowResize();
    }

    function setupButtons(){
        const $start = document.getElementById('start');
        const $stop = document.getElementById('stop');
        $start.addEventListener('click', e => {
            e.preventDefault();
            recorder.start();
            n=0;
            animate();
            $start.style.display = 'none';
            $stop.style.display = 'block';
        }, false);

        $stop.addEventListener('click', e => {
            e.preventDefault();
            recorder.stop();
            n=1;
            animate();
            $stop.style.display = 'none';
            recorder.save();
        }, false);
    }
    setupButtons()
    init();

</script>

</body>
</html>
