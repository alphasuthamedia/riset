@extends('layout.app')

@section('title', 'Historical Sites Reconstruction')

@push('head')
    @vite(['resources/js/mermaid.js', 'resources/js/math.js', 'resources/js/model-viewer.js'])
    <style>
        @keyframes ganti-warna {
            0%   { background-color: #96f27d; }
            20%  { background-color: #edbe77; }
            40%  { background-color: #ffee00; }
            60%  { background-color: #00ff66; }
            80%  { background-color: #00ccff; }
            100% { background-color: #a4ed55; }
        }
        .warna-berganti {
            animation: ganti-warna 3s linear infinite;
        }

    </style>
@endpush

@section('content')
<div class="py-4 font-serif text-lg leading-relaxed">

    {{-- ================= INTRO ================= --}}
    <div class="max-w-7xl mx-auto mb-4">
        <div class="text-center mb-4">
            <p class="inline-block px-3 py-1 text-xl font-bold text-black warna-berganti">ATTENTION!</p>
        </div>

        <div class="space-y-4">
            <p>
                Hello there! I have to admit this is my first "research" project. You might find a lot of
                gaps here, or things the writer still has doubts about.
            </p>
            <p>
                I put "research" in quotes on purpose. Yet not a "researcher", ran the experiment myself, hands-on,
                and I wrote a technical report about what I did and what came out. If something looks wrong,
                it probably is, and I'd be glad to hear about it (contact me <a href="mailto:{{ config('whoami.email') }}" class="underline">here</a>).
            </p>
        </div>
    </div>

    <hr>

    {{-- ================= RESULT: DIPONEGORO ================= --}}
    <div class="max-w-7xl mx-auto">
        <h2 class="mt-6 text-2xl font-bold font-sans">Statue of Pangeran Diponegoro, Monumen Nasional, Jakarta</h2>

        <p class="mt-4">
            Reconstructed from a phone video walking around the statue. Frames go into VGGT, single forward pass,
            no optimization loop. Drag to rotate, scroll to zoom.
        </p>

        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
            <figure>
                <model-viewer
                    src="{{ asset('riset/vggt-historical-site-reconstruction/diponegoro-monas-n-15.glb') }}"
                    alt="3D mesh of the Pangeran Diponegoro statue, color normalized, 15 frames"
                    camera-controls auto-rotate shadow-intensity="1" interaction-prompt="auto" interaction-prompt-threshold="1500"
                    camera-orbit="290deg 75deg 15%"
                    class="w-full h-96 bg-gray-100"
                ></model-viewer>
                <figcaption class="mt-2 text-base font-sans text-gray-600">
                    <strong class="text-black">Color normalized, 15 frames.</strong> Best result.
                </figcaption>
            </figure>

            <figure>
                <model-viewer
                    src="{{ asset('riset/vggt-historical-site-reconstruction/diponegoro-monas-30.glb') }}"
                    alt="3D mesh of the Pangeran Diponegoro statue, plain frames, 30 frames"
                    camera-controls auto-rotate shadow-intensity="1" interaction-prompt="auto" interaction-prompt-threshold="1500"
                    camera-orbit="290deg 75deg 15%"
                    class="w-full h-96 bg-gray-100"
                ></model-viewer>
                <figcaption class="mt-2 text-base font-sans text-gray-600">
                    <strong class="text-black">Plain frames, 30 frames.</strong> Second best.
                </figcaption>
            </figure>
        </div>
    </div>

    <hr class="mt-12">

@verbatim
    <!-- @verbatim: Blade tidak memproses isi blok ini, jadi kurung kurawal LaTeX aman -->
    <div class="max-w-7xl mx-auto">
        <h2 class="mt-6 text-2xl font-bold font-sans">VGGT</h2>

        <div class="mt-4 space-y-2">
            <p>
                VGGT (Visual Geometry Grounded Transformer) is a neural network from Oxford's Visual Geometry Group
                and Meta AI, published at <a href="https://cvpr.thecvf.com/Conferences/2025" class="underline">CVPR 2025</a>.
                It has about 1.2 billion parameters.
            </p>
            <p>
                Give it photos of one scene. For every photo it returns the camera, the depth of every pixel,
                and the 3D position of every pixel.
            </p>
        </div>

      
        <!-- ================= BACKGROUND: DEPTH ================= -->
        <h3 class="mt-12 text-xl font-bold font-sans">Background: Why Depth Can Be Recovered</h3>

        <div class="mt-4 space-y-2">
            <p>
                A photo is flat. It loses depth. But if the camera moves, a point close to the camera shifts a lot
                in the image, and a far point shifts little.
                <a href="https://en.wikipedia.org/wiki/Parallax" class="text-gray-600 underline" target="_blank">[Recall: Parallax]</a>
            </p>
            <p>For two cameras side by side, this becomes one formula:</p>
        </div>

        <div class=" overflow-x-auto">
            $$ Z = \frac{f \, B}{d} $$
        </div>

        <ul class="list-disc pl-6 space-y-1">
            <li>\(Z\): distance of the point from the camera (what we want)</li>
            <li>\(f\): focal length of the lens, in pixels</li>
            <li>\(B\): distance between the two cameras (<em>baseline</em>)</li>
            <li>\(d\): how far the point shifted between the two photos, in pixels (<em>disparity</em>)</li>
        </ul>

        <div class="mt-4 space-y-4">
            <p>
                \(d\) is in the denominator: large shift means close, small shift means far.
                If the camera only rotates without moving, \(B = 0\) and depth cannot be recovered.
            </p>
        </div>

        <!-- ================= BACKGROUND: CLASSIC ================= -->
        <h3 class="mt-12 text-xl font-bold font-sans">Background: The Classic Pipeline</h3>

        <div class="mt-4 space-y-4">
            <p>
                <em>Structure from Motion</em> (SfM, used by tools like COLMAP) works with a camera model.
                A 3D point \(\mathbf{X}\) lands in photo \(i\) at pixel:
            </p>
        </div>

        <div class="my-4 overflow-x-auto">
            $$ \mathbf{x} = \pi\big(K_i \, (R_i \mathbf{X} + \mathbf{t}_i)\big), \qquad \pi\!\begin{pmatrix} a \\ b \\ c \end{pmatrix} = \begin{pmatrix} a/c \\ b/c \end{pmatrix} $$
        </div>

        <ul class="list-disc pl-6 space-y-1">
            <li>\(R_i, \mathbf{t}_i\): camera rotation and translation. \(R_i \mathbf{X} + \mathbf{t}_i\) moves the point into the camera's view.</li>
            <li>\(K_i\): the lens matrix (focal length and image center). It turns the point into pixel units.</li>
            <li>\(\pi\): divide by depth. Far things appear smaller.</li>
        </ul>

        <div class="mt-4 space-y-4">
            <p>
                SfM finds distinctive points in every photo, matches them across photos, and makes a first guess of
                all cameras and 3D points. Then <em>bundle adjustment</em> refines that guess by solving:
            </p>
        </div>

        <div class="my-4 overflow-x-auto">
            $$ \min_{\{R_i,\, \mathbf{t}_i,\, K_i\},\; \{\mathbf{X}_j\}} \; \sum_{i,j} \rho\Big( \big\| \mathbf{x}_{ij} - \pi\big(K_i (R_i \mathbf{X}_j + \mathbf{t}_i)\big) \big\|^2 \Big) $$
        </div>

        <ul class="list-disc pl-6 space-y-1">
            <li>\(\mathbf{x}_{ij}\): where point \(j\) was actually found in photo \(i\). Fixed, never changed.</li>
            <li>The term inside \(\| \cdot \|\) is the
                <a href="https://support.pix4d.com/hc/en-us/articles/202559369" target="_blank" class="underline">reprojection error</a>:
                pixel distance between where the point should land and where it was found.</li>
            <li>\(\rho\): a robust function that reduces the influence of wrong matches. (IMO, it's easier to picture it as soft clipping: small errors pass through unchanged, large ones get dampened instead of cut off) </li>
            <li>The solver nudges all cameras and points together, many times, until the total error stops dropping.</li>
        </ul>

        <div class="mt-4 space-y-4">
            <p>
                The result is cameras plus a <em>sparse</em> set of 3D points (only at matched points).
                Dense depth for every pixel needs a second stage, <em>Multi-View Stereo</em> (MVS).
            </p>
            <p>
                VGGT replaces both stages with <strong>a single network</strong>.
                The photos pass through it once (<em>feed-forward</em>). No optimization loop is needed,
                although bundle adjustment can still be added afterwards.
            </p>
        </div>

<pre class="mermaid my-8 flex justify-center">
flowchart LR
    subgraph S["Classic pipeline"]
        direction TB
        S1["Find distinctive points<br/>in each photo"] --> S2["Match them<br/>between photos"]
        S2 --> S3["First guess:<br/>cameras + 3D points"]
        S3 --> S4["Bundle adjustment<br/>(refine the guess)"]
        S4 -.-> S4
        S4 --> S5["Sparse points + cameras"]
        S5 --> S6["Multi-View Stereo"]
        S6 --> S7["Dense depth + point cloud"]
    end
    subgraph V["VGGT"]
        direction TB
        V1["All photos"] --> V2["One pass through<br/>the network"]
        V2 --> V3["Cameras + dense depth<br/>+ point cloud"]
    end
    S ~~~ V
</pre>

        <!-- ================= OVERVIEW ================= -->
        <h3 class="mt-12 text-xl font-bold font-sans">What VGGT Computes</h3>

        <div class="mt-4 space-y-4">
            <p>VGGT is one function \(f\) that maps all photos to four outputs per photo:</p>
        </div>

        <div class="my-4 overflow-x-auto">
            $$ f\big( (I_i)_{i=1}^{N} \big) = \big( \mathbf{g}_i,\; D_i,\; P_i,\; T_i \big)_{i=1}^{N} $$
        </div>

        <table class="mt-4 w-full border text-base font-sans">
            <tr class="bg-gray-100">
                <th class="border p-2 text-left">Output</th>
                <th class="border p-2 text-left">Shape</th>
                <th class="border p-2 text-left">Meaning</th>
            </tr>
            <tr><td class="border p-2">\(\mathbf{g}_i\)</td><td class="border p-2">\(\mathbb{R}^{9}\)</td><td class="border p-2">camera of photo \(i\)</td></tr>
            <tr><td class="border p-2">\(D_i\)</td><td class="border p-2">\(\mathbb{R}^{H \times W}\)</td><td class="border p-2">depth map: how far in front of the camera each pixel is, along the viewing axis (z)</td></tr>
            <tr><td class="border p-2">\(P_i\)</td><td class="border p-2">\(\mathbb{R}^{3 \times H \times W}\)</td><td class="border p-2">point map: 3D position per pixel, in photo 1's coordinates</td></tr>
            <tr><td class="border p-2">\(T_i\)</td><td class="border p-2">\(\mathbb{R}^{C \times H \times W}\)</td><td class="border p-2">tracking features, used by the track head</td></tr>
        </table>

        <div class="mt-4 space-y-4">
            <p>Inside, three stages: tokenize, mix with attention, read out with heads.</p>
        </div>

<pre class="mermaid my-8 flex justify-center">
flowchart TB
    A["N photos"] --> B["<b>Tokenize</b><br/>DINO patch tokens<br/>+ 1 camera token + 4 register tokens"]
    B --> C["<b>Backbone</b><br/>24 × (frame attention → global attention)"]
    C --> D["<b>Camera head</b><br/>reads camera tokens"]
    C --> E["<b>Dense head (DPT)</b><br/>reads patch tokens"]
    D --> F["g: rotation, translation,<br/>field of view"]
    E --> G["D, P + confidence<br/>per pixel"]
    E --> H["<b>Track head</b><br/>reads T"]
    H --> I["2D position of a point<br/>in every photo"]
</pre>

        <!-- ================= TOKENIZE ================= -->
        <h3 class="mt-12 text-xl font-bold font-sans">Tokenize</h3>

        <div class="mt-4 space-y-4">
            <p>
                A transformer reads <em>tokens</em>: vectors of \(C\) numbers, each describing one small piece of the input.
            </p>
            <p>
                Each photo is resized to a longest side of 518 px and cut into small square patches.
                A pretrained image model, <a href="https://arxiv.org/pdf/2104.14294" class="underline">DINO</a>,
                turns every patch into a token:
            </p>
        </div>

        <div class="my-4 overflow-x-auto">
            $$ \mathbf{t}^{I}_i = \text{DINO}(I_i) \in \mathbb{R}^{m \times C} $$
        </div>

        <div class="space-y-4">
            <p>
                \(m\) is the number of patches; it depends on image size.
                Then two kinds of learned tokens are added in front:
            </p>
        </div>

        <div class="my-4 overflow-x-auto">
            $$ \mathbf{X}_i = \big[\; \mathbf{t}^{g}_i \;,\; \mathbf{t}^{R}_i \;,\; \mathbf{t}^{I}_i \;\big] \in \mathbb{R}^{n \times C}, \qquad n = 1 + 4 + m $$
        </div>

        <div class="mt-4 space-y-4">
            <p>
                Photo 1 gets its own learned camera and register tokens. Photos 2 to \(N\) share a second set.
                This tells the model which photo is the reference: all 3D output is in photo 1's frame.
                Photos 2 to \(N\) start with identical extra tokens and only become distinct through attention,
                so their order does not matter.
            </p>
        </div>

        <!-- ================= ATTENTION ================= -->
        <h3 class="mt-12 text-xl font-bold font-sans">Attention</h3>

        <div class="mt-4 space-y-4">
            <p>
                <em>Attention</em> lets each token pull information from other tokens.
                Given tokens \(\mathbf{X}\), three learned matrices make three views of them:
            </p>
        </div>

        <div class="my-4 overflow-x-auto">
            $$ Q = \mathbf{X} W_Q, \qquad K = \mathbf{X} W_K, \qquad V = \mathbf{X} W_V $$
            $$ \text{Attn}(\mathbf{X}) = \text{softmax}\!\left( \frac{Q K^{\top}}{\sqrt{C}} \right) V $$
        </div>  

        <div class="mt-4 space-y-4">
            <p>
                Example: the token for the statue's nose in photo 2 gives a high score to the nose token in photo 5,
                and a low score to a sky token. It then absorbs mostly the nose information.
            </p>
        </div>

        <!-- ================= ALTERNATING ================= -->
        <h3 class="mt-12 text-xl font-bold font-sans">Alternating Attention</h3>

        <div class="mt-4 space-y-4">
            <p>
                All tokens together form a tensor of shape \(N \times n \times C\).
                VGGT applies the same attention operation to two different groupings of it:
            </p>
        </div>

        <table class="mt-4 w-full border text-base font-sans">
            <tr class="bg-gray-100">
                <th class="border p-2 text-left"></th>
                <th class="border p-2 text-left">Frame attention</th>
                <th class="border p-2 text-left">Global attention</th>
            </tr>
            <tr>
                <td class="border p-2">Grouping</td>
                <td class="border p-2">\(N\) separate sequences of shape \(n \times C\)</td>
                <td class="border p-2">1 sequence of shape \(Nn \times C\)</td>
            </tr>
            <tr>
                <td class="border p-2">A token sees</td>
                <td class="border p-2">tokens of its own photo</td>
                <td class="border p-2">tokens of all photos</td>
            </tr>
            <tr>
                <td class="border p-2">Learns</td>
                <td class="border p-2">each image alone: edges, shapes, what is in front of what</td>
                <td class="border p-2">the same spot across photos, which is what parallax needs</td>
            </tr>
            <tr>
                <td class="border p-2">Score pairs</td>
                <td class="border p-2">\(N \cdot n^2\)</td>
                <td class="border p-2">\((Nn)^2 = N^2 n^2\)</td>
            </tr>
        </table>

        <div class="mt-4 space-y-4">
            <p>The backbone has \(L = 24\) layers. Each layer runs one of each, in order:</p>
        </div>

        <div class="my-4 overflow-x-auto">
            $$ \mathbf{X} \;\leftarrow\; \text{GlobalAttn}\big( \text{FrameAttn}(\mathbf{X}) \big) \qquad \text{repeated } 24 \text{ times} $$
        </div>

        <div class="space-y-4">
            <p>
                Global attention costs \(N\) times more than frame attention, because it compares every token with
                every token in every photo. This is why memory grows quickly with more photos.
            </p>
            <p>
                There are no cross-attention layers and no hand-written 3D rules. All geometry is learned from data.
            </p>
        </div>

<pre class="mermaid my-8 flex justify-center">
flowchart LR
    subgraph FA["Frame attention: N × (n × C)"]
        direction TB
        F1["Photo 1 ↔ Photo 1"]
        F2["Photo 2 ↔ Photo 2"]
        F3["Photo 3 ↔ Photo 3"]
    end
    subgraph GA["Global attention: (Nn) × C"]
        direction TB
        G1["Photo 1 ↔ Photo 2 ↔ Photo 3<br/>every token sees every token"]
    end
    FA --> GA
    GA -- "repeat × 24" --> FA
</pre>

        <!-- ================= CAMERA HEAD ================= -->
        <h3 class="mt-12 text-xl font-bold font-sans">Camera Head</h3>

        <div class="mt-4 space-y-4">
            <p>
                A <em>head</em> is a small network that turns tokens into one kind of answer.
                The camera head reads each photo's camera token after the backbone,
                passes it through 4 more self-attention layers and one linear layer:
            </p>
        </div>

        <div class="my-4 overflow-x-auto">
            $$ \hat{\mathbf{g}}_i = \text{Linear}\big( \text{SelfAttn}^{\times 4}(\hat{\mathbf{t}}^{g}_i) \big) = \big[\, \mathbf{q}_i \in \mathbb{R}^4,\; \mathbf{t}_i \in \mathbb{R}^3,\; \mathbf{f}_i \in \mathbb{R}^2 \,\big] $$
        </div>

        <p class="mt-4"><strong>Rotation \(\mathbf{q} = (x, y, z, w)\).</strong>
            A <em>quaternion</em>: four numbers that encode a 3D rotation. It converts to a rotation matrix:</p>

        <div class="my-4 overflow-x-auto">
            $$ R(\mathbf{q}) = \begin{pmatrix}
                1 - 2(y^2 + z^2) & 2(xy - zw) & 2(xz + yw) \\
                2(xy + zw) & 1 - 2(x^2 + z^2) & 2(yz - xw) \\
                2(xz - yw) & 2(yz + xw) & 1 - 2(x^2 + y^2)
            \end{pmatrix} $$
        </div>

        <p>Quaternions are used instead of 9 matrix entries because 4 numbers are enough and they avoid invalid rotations.</p>

        <p class="mt-4"><strong>Translation \(\mathbf{t}\).</strong>
            Together with \(R\), it maps a world point into this camera's view: \(\mathbf{X}_{\text{cam}} = R\,\mathbf{X} + \mathbf{t}\).</p>

        <p class="mt-4"><strong>Field of view \(\mathbf{f} = (\theta_w, \theta_h)\).</strong>
            How wide the lens sees, horizontally and vertically. It gives the lens matrix:</p>

        <div class="my-4 overflow-x-auto">
            $$ f_x = \frac{W}{2 \tan(\theta_w / 2)}, \qquad f_y = \frac{H}{2 \tan(\theta_h / 2)}, \qquad
               K = \begin{pmatrix} f_x & 0 & W/2 \\ 0 & f_y & H/2 \\ 0 & 0 & 1 \end{pmatrix} $$
        </div>

        <ul class="list-disc pl-6 space-y-1">
            <li>Wide lens (large \(\theta\)) gives small \(f\): more of the scene fits in the photo.</li>
            <li>The <em>principal point</em> (where the lens axis hits the image) is assumed at the center \((W/2, H/2)\).</li>
            <li class="font-semibold">Lens distortion is not modeled.</li>
            <li>Photo 1 is the reference: \(\mathbf{q}_1 = (0, 0, 0, 1)\) (no rotation), \(\mathbf{t}_1 = \mathbf{0}\).</li>
        </ul>

        <!-- ================= DENSE HEAD ================= -->
        <h3 class="mt-12 text-xl font-bold font-sans">Dense Head (DPT)</h3>

        <div class="mt-4 space-y-4">
            <p>
                Patch tokens are coarse: one token covers a whole patch of pixels.
                DPT (Dense Prediction Transformer) rearranges the patch tokens back into an image-shaped grid
                and upsamples it step by step to full resolution, producing a feature map \(F_i\).
                A final 3×3 convolution turns \(F_i\) into:
            </p>
        </div>

        <div class="my-4 overflow-x-auto">
            $$ F_i \;\longrightarrow\; \hat{D}_i,\; \Sigma^{D}_i,\; \hat{P}_i,\; \Sigma^{P}_i,\; T_i $$
        </div>

        <ul class="list-disc pl-6 space-y-1">
            <li>\(\hat{D}_i\): depth map.</li>
            <li>\(\hat{P}_i\): point map, in photo 1's frame.</li>
            <li>\(\Sigma^{D}_i, \Sigma^{P}_i\): one positive number per pixel for each map. Learned during training (see below); afterwards it works as <em>confidence</em>.</li>
            <li>\(T_i\): tracking features.</li>
        </ul>

        <!-- ================= TRACK HEAD ================= -->
        <h3 class="mt-12 text-xl font-bold font-sans">Track Head</h3>

        <div class="mt-4 space-y-4">
            <p>
                Follows one point across all photos. Built on the CoTracker2 design.
            </p>
            <ol class="list-decimal pl-6 space-y-1">
                <li>Pick a query pixel \(\mathbf{y}_q\) in one photo.</li>
                <li>Read its feature from \(T_q\) by bilinear sampling (it may fall between pixels): \(\mathbf{h} = T_q(\mathbf{y}_q)\).</li>
                <li>Compare \(\mathbf{h}\) with every pixel feature of every other photo \(i\). High similarity marks likely matches.</li>
                <li>Self-attention layers refine these matches into a 2D position \(\hat{\mathbf{y}}_i\) and a visibility score for each photo.</li>
            </ol>
            <p>
                It does not assume the photos are in time order, so it works on unordered photos, not just video.
            </p>
        </div>

        <!-- ================= TO 3D ================= -->
        <h3 class="mt-12 text-xl font-bold font-sans">From Output to Point Cloud</h3>

        <div class="mt-4 space-y-4">
            <p>
                The point map \(\hat{P}\) already holds 3D points. But the paper reports a <strong>more accurate</strong> route:
                build the points from depth and camera instead (<em>unprojection</em>).
                For pixel \((u, v)\) of photo \(i\):
            </p>
        </div>

        <div class="my-4 overflow-x-auto">
            $$ \mathbf{X}_{\text{cam}} = \hat{D}_i(u, v) \cdot K_i^{-1} \begin{pmatrix} u \\ v \\ 1 \end{pmatrix}
               \qquad\qquad
               \mathbf{X}_{\text{world}} = R_i^{\top} \big( \mathbf{X}_{\text{cam}} - \mathbf{t}_i \big) $$
        </div>

        <ul class="list-disc pl-6 space-y-1">
            <li>\(K_i^{-1}[u, v, 1]^{\top}\): the viewing direction through that pixel.</li>
            <li>Multiply by depth: walk that far along the direction. Now the point is relative to camera \(i\).</li>
            <li>\(R_i^{\top}(\cdot - \mathbf{t}_i)\): undo the camera transform. Now the point is in photo 1's frame, shared by all photos.</li>
        </ul>

        <div class="mt-4 space-y-4">
            <p>Then keep only confident pixels:</p>
        </div>

        <div class="my-4 overflow-x-auto">
            $$ \text{keep } (u, v) \;\text{ if }\; \Sigma^{D}_i(u, v) \ge \tau $$
        </div>

<pre class="mermaid my-8 flex justify-center">
flowchart TB
    A["Pixel (u, v)"] --> C
    B["Depth D(u, v)"] --> C
    C["Point relative to camera<br/>X_cam = D · K⁻¹ · [u, v, 1]"] --> D
    D["Point in shared frame<br/>X_world = Rᵀ · (X_cam − t)"] --> E
    E{"Confidence ≥ τ ?"} -- yes --> F["Keep → point cloud"]
    E -- no --> G["Drop"]
</pre>

        <div class="space-y-4">
            <p>
                Repeat for every pixel of every photo. The result is a <em>point cloud</em>: colored dots in 3D,
                not a solid surface. In this experiment the point cloud is turned into a mesh afterwards (see Experiment).
            </p>
            <p>
                The threshold \(\tau\) is a trade-off: higher gives a cleaner but emptier cloud, lower gives a fuller but noisier one.
            </p>
        </div>

        <!-- ================= LOSS ================= -->
        <h3 class="mt-12 text-xl font-bold font-sans">Training Objective</h3>

        <div class="mt-4 space-y-4">
            <p>
                Training shows the network scenes where the true cameras, depths, points, and tracks are known.
                A <em>loss</em> measures how wrong the predictions are. Training lowers it.
            </p>
        </div>

        <div class="my-4 overflow-x-auto">
            $$ \mathcal{L} = \mathcal{L}_{\text{camera}} + \mathcal{L}_{\text{depth}} + \mathcal{L}_{\text{pmap}} + \lambda\, \mathcal{L}_{\text{track}}, \qquad \lambda = 0.05 $$
        </div>

        <div class="space-y-4">
            <p>
                The first three are on similar scales, so they get equal weight. Tracking gets a low weight.
            </p>
        </div>

        <p class="mt-6"><strong>Camera loss.</strong></p>

        <div class="my-4 overflow-x-auto">
            $$ \mathcal{L}_{\text{camera}} = \sum_{i=1}^{N} \big\| \hat{\mathbf{g}}_i - \mathbf{g}_i \big\|_{\epsilon} $$
        </div>

        <p>
            \(\|\cdot\|_{\epsilon}\) is the <em>Huber loss</em>. For an error \(r\):
        </p>

        <div class="my-4 overflow-x-auto">
            $$ \rho_\epsilon(r) = \begin{cases} \tfrac{1}{2} r^2 & |r| \le \epsilon \\[4pt] \epsilon \big( |r| - \tfrac{1}{2}\epsilon \big) & |r| > \epsilon \end{cases} $$
        </div>

        <p>Squared for small errors (smooth), linear for large ones (a few bad samples cannot dominate).</p>

        <p class="mt-6"><strong>Depth loss.</strong></p>

        <div class="my-4 overflow-x-auto">
            $$ \mathcal{L}_{\text{depth}} = \sum_{i=1}^{N}
               \underbrace{\big\| \Sigma^{D}_i \odot (\hat{D}_i - D_i) \big\|}_{\text{(1) error}}
             + \underbrace{\big\| \Sigma^{D}_i \odot (\nabla \hat{D}_i - \nabla D_i) \big\|}_{\text{(2) gradient error}}
             - \underbrace{\alpha \log \Sigma^{D}_i}_{\text{(3) uncertainty cost}} $$
        </div>

        <ul class="list-disc pl-6 space-y-1">
            <li>\(\odot\): multiply pixel by pixel.</li>
            <li>(1) Depth error, weighted per pixel by \(\Sigma^{D}_i\).</li>
            <li>(2) \(\nabla\) is the change between neighboring pixels. Matching it keeps edges sharp and slopes correct.</li>
            <li>(3) Stops the model from cheating. Setting \(\Sigma\) small would shrink (1) and (2), but makes \(-\log \Sigma\) large.</li>
            <li>Net effect: on hard pixels the model lowers \(\Sigma\) and pays a bit of (3); on easy pixels it keeps \(\Sigma\) high. That is why \(\Sigma\) ends up as a confidence map.</li>
        </ul>

        <p class="mt-6"><strong>Point-map loss.</strong> Same form as depth, with \(\hat{P}_i, P_i, \Sigma^{P}_i\).</p>

        <p class="mt-6"><strong>Track loss.</strong></p>

        <div class="my-4 overflow-x-auto">
            $$ \mathcal{L}_{\text{track}} = \sum_{j=1}^{M} \sum_{i=1}^{N} \big\| \mathbf{y}_{j,i} - \hat{\mathbf{y}}_{j,i} \big\| $$
        </div>

        <p>
            For \(M\) query points and \(N\) photos: distance between true and predicted 2D position.
            A visibility term (binary cross-entropy) is added, following CoTracker2.
        </p>

        <p class="mt-6"><strong>Normalization.</strong> Ground truth is expressed in photo 1's frame, then scaled by:</p>

        <div class="my-4 overflow-x-auto">
            $$ s = \frac{1}{|\mathcal{X}|} \sum_{\mathbf{x} \in \mathcal{X}} \|\mathbf{x}\|, \qquad
               \mathbf{t} \leftarrow \mathbf{t}/s,\quad P \leftarrow P/s,\quad D \leftarrow D/s $$
        </div>

        <p>
            \(\mathcal{X}\) is the set of all true 3D points of the scene. After scaling, the average point is at distance 1 from the origin.
            Predictions are not rescaled; the network learns this convention.
            This is why VGGT output has no real-world units.
        </p>

    </div>
@endverbatim

    <hr class="mt-16">

    {{-- ================= EXPERIMENT ================= --}}
    <div class="max-w-7xl mx-auto">
        <h2 class="mt-6 text-2xl font-bold font-sans">Experiment</h2>

        <div class="mt-4 space-y-4">
            <p>
                Three subjects in Jakarta, each filmed as a walk-around video on a phone:
                <strong>Bundaran HI</strong> (the Selamat Datang Monument), <strong>Monumen Nasional</strong> (the Monas obelisk),
                and the <strong>Pangeran Diponegoro statue</strong> at Monas.
                The videos, sampled frames, external photos, and all notebooks are in the
                <a href="https://github.com/alphasuthamedia/vggt" class="underline" target="_blank">experiment repository</a>.
            </p>
        </div>

        <h3 class="mt-12 text-xl font-bold font-sans">Pipeline</h3>

<pre class="mermaid my-8 flex justify-center">
flowchart TB
    A["Walk-around video"] --> B["<b>Frame sampling</b><br/>sharpest frame per interval"]
    B --> C["<b>Color normalization</b><br/>(optional)"]
    C --> D["<b>Sky removal</b><br/>(optional, Monas only)<br/>paint sky white"]
    D --> E["<b>VGGT</b><br/>single forward pass:<br/>depth + camera"]
    E --> F["<b>Unproject</b><br/>keep top 50% confidence<br/>(+ drop sky pixels)"]
    F --> G["<b>Clean</b><br/>downsample + remove outliers"]
    G --> H["<b>Poisson mesh</b>"]
    H --> I["GLB"]
</pre>

@verbatim
        <div class="space-y-4">
            <p class="mt-8"><strong>1. Frame sampling</strong> <span class="font-mono text-base text-gray-600">video360_ke_foto.ipynb</span></p>
            <p>
                A walk-around video has thousands of nearly identical frames, many blurred by motion.
                I split the video into N equal intervals and keep only the sharpest frame of each.
            </p>
            <pre class="mt-2 overflow-x-auto bg-gray-100 p-4 font-mono text-sm leading-snug"><code>jarak = total // JUMLAH                     # frames per interval

for i in range(total):
    ok, frame = cap.read()
    gray = cv2.cvtColor(frame, cv2.COLOR_BGR2GRAY)
    skor = cv2.Laplacian(gray, cv2.CV_64F).var()   # sharpness score

    if skor > skor_terbaik:                      # remember the sharpest so far
        terbaik, skor_terbaik = frame, skor

    if (i + 1) % jarak == 0:                     # interval ends: keep the winner
        cv2.imwrite(f"{OUT}/{n:03d}.png", terbaik)
        terbaik, skor_terbaik = None, 0</code></pre>
            <ul class="list-disc pl-6 space-y-1">
                <li>The <em>Laplacian</em> highlights edges. A sharp frame has many strong edges, so the variance of the Laplacian is high; a blurry frame scores low.</li>
                <li>Bundaran HI: 13,923 frames (232 s at 60 fps). For 15 photos, one pick per 928 frames.</li>
            </ul>

            <p class="mt-8"><strong>2. Stitching the Monas video</strong> <span class="font-mono text-base text-gray-600">append_monas.ipynb</span></p>
            <p>Monas was filmed in three clips. I join them into one video before sampling.</p>
            <pre class="mt-2 overflow-x-auto bg-gray-100 p-4 font-mono text-sm leading-snug"><code>tulis("monas-1.mp4")
tulis("monas-2.mp4")
tulis("monas-3-reversed.mp4", balik=True)   # written back to front
# every frame is resized to 1080 × 1920 (portrait)</code></pre>

            <p class="mt-8"><strong>3. Color normalization</strong> <span class="font-mono text-base text-gray-600">01_color_normalization.ipynb</span></p>
            <p>
                Exposure and white balance drift while filming, so the same surface can look different between frames.
                I reshape every frame's colors to match one reference frame.
            </p>
            <pre class="mt-2 overflow-x-auto bg-gray-100 p-4 font-mono text-sm leading-snug"><code>reference = frames[len(frames) // 2]           # middle frame of the set
result = match_histograms(image, reference, channel_axis=-1)</code></pre>
            <ul class="list-disc pl-6 space-y-1">
                <li><em>Histogram matching</em>: for each channel (R, G, B), pixel values are remapped so their distribution matches the reference.</li>
                <li>It changes color only. Geometry, shadows, and lighting direction stay the same.</li>
            </ul>

            <p class="mt-8"><strong>4. VGGT forward pass</strong> <span class="font-mono text-base text-gray-600">vggt_to_mesh.ipynb</span></p>
            <pre class="mt-2 overflow-x-auto bg-gray-100 p-4 font-mono text-sm leading-snug"><code>model = VGGT.from_pretrained("facebook/VGGT-1B").to("cuda").eval()
model.track_head = None        # not needed for a 3D model
model.point_head = None        # use depth + camera instead (more accurate)

images = load_and_preprocess_images(paths, mode="pad")   # (S, 3, H, W)
with torch.autocast("cuda", dtype=torch.bfloat16):
    pred = model(images)                                  # the single forward pass

extr, intr = pose_encoding_to_extri_intri(pred["pose_enc"], images.shape[-2:])
depth = pred["depth"]          # D, per pixel
conf  = pred["depth_conf"]     # Σ^D, per pixel (≥ 1)</code></pre>
            <ul class="list-disc pl-6 space-y-1">
                <li><span class="font-mono text-base">S</span> is the number of photos (\(N\) in the formulas above).</li>
                <li><span class="font-mono text-base">mode="pad"</span>: the longest side becomes 518 px and the short side is padded to a square, so nothing is cropped.</li>
                <li>bfloat16 halves memory with little loss in accuracy.</li>
                <li><span class="font-mono text-base">pose_enc</span> holds the 9 camera numbers, ordered translation (3), quaternion (4), field of view (2).
                    <span class="font-mono text-base">pose_encoding_to_extri_intri</span> turns them into \([R \mid \mathbf{t}]\) and \(K\), using the image size.</li>
            </ul>

            <p class="mt-8"><strong>5. Depth to points, filtered by confidence</strong></p>
            <pre class="mt-2 overflow-x-auto bg-gray-100 p-4 font-mono text-sm leading-snug"><code>pts  = unproject_depth_map_to_point_map(depth, extr, intr)   # (S, H, W, 3)
keep = conf > np.percentile(conf, 50)                         # top 50% most confident
points, colors = pts[keep], rgb[keep]                         # flat list of (x, y, z)</code></pre>
            <ul class="list-disc pl-6 space-y-1">
                <li><span class="font-mono text-base">unproject_depth_map_to_point_map</span> is the formula in "From Output to Point Cloud": \(\mathbf{X} = R^{\top}(D \cdot K^{-1}[u, v, 1]^{\top} - \mathbf{t})\) for every pixel.</li>
                <li>Instead of a fixed \(\tau\), I keep the most confident half of all pixels across all photos.</li>
            </ul>

            <p class="mt-8"><strong>6. Sky removal</strong> <span class="font-mono text-base text-gray-600">vggt_to_mesh.ipynb</span> <span class="text-base text-gray-600">(Monas runs only)</span></p>
            <p>
                A sky segmentation model marks which pixels are sky. Those pixels are painted white <strong>before</strong> VGGT,
                so the model never sees the sky, and they are dropped again from the point cloud afterwards.
            </p>
            <pre class="mt-2 overflow-x-auto bg-gray-100 p-4 font-mono text-sm leading-snug"><code>sky_session = onnxruntime.InferenceSession("skyseg.onnx")   # from huggingface: JianyuanWang/skyseg

def not_sky(img):
    x = cv2.resize(img, (320, 320))                            # model input size
    x = (x - [0.485, 0.456, 0.406]) / [0.229, 0.224, 0.225]     # ImageNet normalization
    out = sky_session.run(None, {"input": x})[0]               # sky score per pixel
    out = (out - out.min()) / (out.max() - out.min()) * 255    # rescale to 0..255
    return cv2.resize(out, (w, h)) &lt; 32                        # True = not sky

# before VGGT: paint the sky white
sky = ~np.stack([not_sky(im) for im in images.permute(0, 2, 3, 1).cpu().numpy()])
images = images.masked_fill(torch.from_numpy(sky).to(images.device)[:, None], 1.0)

pred = model(images)                                         # VGGT sees white instead of sky

# after VGGT: drop sky pixels from the point cloud too
keep &amp;= ~sky</code></pre>
            <ul class="list-disc pl-6 space-y-1">
                <li>The mask is computed on the already resized input (518 px), so it lines up pixel for pixel with the depth map. No extra resizing needed.</li>
                <li>A pixel counts as sky when its rescaled score is 32 or higher (out of 255).</li>
                <li>White is a neutral choice: with <span class="font-mono text-base">mode="pad"</span>, VGGT already fills the padding with a plain color.</li>
            </ul>

            <p class="mt-8"><strong>7. Remember which camera saw each point</strong></p>
            <pre class="mt-2 overflow-x-auto bg-gray-100 p-4 font-mono text-sm leading-snug"><code>cams = -np.einsum("sji,sj->si", R, t)      # camera centers: C = −Rᵀ t
view = (cams[:, None, None] - pts)[keep]     # direction: point → its camera</code></pre>
            <ul class="list-disc pl-6 space-y-1">
                <li>A camera's center is where \(\mathbf{X}_{\text{cam}} = \mathbf{0}\). Solving \(R\mathbf{C} + \mathbf{t} = \mathbf{0}\) gives \(\mathbf{C} = -R^{\top}\mathbf{t}\).</li>
                <li>This direction is needed later to decide which side of the surface faces outward.</li>
            </ul>

            <p class="mt-8"><strong>8. Clean the point cloud</strong></p>
            <pre class="mt-2 overflow-x-auto bg-gray-100 p-4 font-mono text-sm leading-snug"><code>pcd = pcd.voxel_down_sample(size / 1000)          # merge points inside small cubes
pcd, _ = pcd.remove_statistical_outlier(20, 2.0)    # drop floating points</code></pre>
            <ul class="list-disc pl-6 space-y-1">
                <li><em>Voxel downsampling</em>: space is cut into cubes 1/1000 of the scene size; points in one cube become one point. Millions of points shrink to hundreds of thousands without losing the shape.</li>
                <li><em>Statistical outlier removal</em>: a point is dropped if its average distance to its 20 nearest neighbors is more than 2 standard deviations above normal.</li>
            </ul>

            <p class="mt-8"><strong>9. Build the mesh</strong></p>
            <pre class="mt-2 overflow-x-auto bg-gray-100 p-4 font-mono text-sm leading-snug"><code>pcd.estimate_normals()
n[(n * view).sum(1) &lt; 0] *= -1                # flip normals that point away from the camera

mesh, dens = create_from_point_cloud_poisson(pcd, depth=9)
mesh.remove_vertices_by_mask(dens &lt; np.quantile(dens, 0.05))   # drop invented surface
mesh = mesh.crop(pcd.get_axis_aligned_bounding_box())</code></pre>
            <ul class="list-disc pl-6 space-y-1">
                <li>A <em>normal</em> is the direction a surface faces. Estimated from neighbors, it can point in or out at random.
                    A surface seen by a camera must face that camera, so if the normal and the point-to-camera direction disagree (dot product &lt; 0), it is flipped.</li>
                <li><em>Poisson surface reconstruction</em> wraps one closed surface around the points. <span class="font-mono text-base">depth=9</span> means a 2⁹ = 512 grid per side.</li>
                <li>Poisson also fills areas with no data. <span class="font-mono text-base">dens</span> counts how many real points support each vertex; the weakest 5% are removed, then the mesh is cut to the point cloud's bounding box.</li>
            </ul>

            <p class="mt-8"><strong>10. Export</strong></p>
            <pre class="mt-2 overflow-x-auto bg-gray-100 p-4 font-mono text-sm leading-snug"><code>vertices * [1, -1, -1]      # OpenCV (y down, z forward) → glTF (y up, z back)</code></pre>
            <p>Without this flip the model opens upside down in <span class="font-mono text-base">model-viewer</span>, three.js, or Blender.</p>
        </div>
@endverbatim

        <h3 class="mt-12 text-xl font-bold font-sans">Runs</h3>

        <p class="mt-4">Numbers are frame counts. Green marks a usable model; darker is better.</p>

        <div class="mt-4 overflow-x-auto">
        <table class="w-full border text-base font-sans text-center">
            <tr class="bg-gray-100">
                <th class="border p-2 text-left">Plain</th>
                <th class="border p-2">15</th><th class="border p-2">30</th><th class="border p-2">60</th>
                <th class="border p-2">120</th><th class="border p-2">240</th><th class="border p-2">360</th>
            </tr>
            <tr><td class="border p-2 text-left">Bundaran HI</td><td class="border p-2 text-red-600">✗</td><td class="border p-2 text-red-600">✗</td><td class="border p-2 text-red-600">✗</td><td class="border p-2 text-red-600">✗</td><td class="border p-2 text-red-600">✗</td><td class="border p-2 text-red-600">✗</td></tr>
            <tr><td class="border p-2 text-left">Monumen Nasional</td><td class="border p-2 text-red-600">✗</td><td class="border p-2 text-red-600">✗</td><td class="border p-2 text-red-600">✗</td><td class="border p-2 text-red-600">✗</td><td class="border p-2 text-red-600">✗</td><td class="border p-2 text-red-600">✗</td></tr>
            <tr><td class="border p-2 text-left">Diponegoro statue</td><td class="border p-2">✓</td><td class="border p-2 bg-green-200">✓ good</td><td class="border p-2">✓</td><td class="border p-2">✓</td><td class="border p-2">✓</td><td class="border p-2">✓</td></tr>
            <tr class="bg-gray-100">
                <th class="border p-2 text-left">Color normalized</th>
                <th class="border p-2">15</th><th class="border p-2">30</th><th class="border p-2">60</th>
                <th class="border p-2">120</th><th class="border p-2">240</th><th class="border p-2">360</th>
            </tr>
            <tr><td class="border p-2 text-left">Bundaran HI</td><td class="border p-2 text-red-600">✗</td><td class="border p-2 text-red-600">✗</td><td class="border p-2 text-red-600">✗</td><td class="border p-2 text-red-600">✗</td><td class="border p-2 text-gray-400">–</td><td class="border p-2 text-gray-400">–</td></tr>
            <tr><td class="border p-2 text-left">Monumen Nasional</td><td class="border p-2 text-red-600">✗</td><td class="border p-2 text-red-600">✗</td><td class="border p-2 text-red-600">✗</td><td class="border p-2 text-red-600">✗</td><td class="border p-2 text-gray-400">–</td><td class="border p-2 text-gray-400">–</td></tr>
            <tr><td class="border p-2 text-left">Diponegoro statue</td><td class="border p-2 bg-green-600 text-white">✓ best</td><td class="border p-2">✓</td><td class="border p-2">✓</td><td class="border p-2">✓</td><td class="border p-2 text-gray-400">–</td><td class="border p-2 text-gray-400">–</td></tr>
            <tr class="bg-gray-100">
                <th class="border p-2 text-left">Color normalized + sky removal</th>
                <th class="border p-2">15</th><th class="border p-2">30</th><th class="border p-2">60</th>
                <th class="border p-2">120</th><th class="border p-2" colspan="2"></th>
            </tr>
            <tr><td class="border p-2 text-left">Monumen Nasional</td><td class="border p-2 text-red-600">✗</td><td class="border p-2 text-red-600">✗</td><td class="border p-2 text-red-600">✗</td><td class="border p-2 text-red-600">✗</td><td class="border p-2" colspan="2"></td></tr>

            <tr class="bg-gray-100">
                <th class="border p-2 text-left">Sky removal + external photos</th>
                <th class="border p-2">4 + 59</th><th class="border p-2">60 + 59</th><th class="border p-2">60 + 59 N</th>
                <th class="border p-2">4 + 59 N</th><th class="border p-2" colspan="2"></th>
            </tr>
            <tr><td class="border p-2 text-left">Monumen Nasional</td><td class="border p-2 text-red-600">✗</td><td class="border p-2 text-red-600">✗</td><td class="border p-2 text-red-600">✗</td><td class="border p-2 text-red-600">✗</td><td class="border p-2" colspan="2"></td></tr>
        </table>
        </div>

        <ul class="mt-4 list-disc pl-6 space-y-1 text-base">
            <li>"4 + 59" = 4 frames from the Monas video + 59 openly available photos of Monas. N = color normalized.</li>
            <li>Hardware: NVIDIA L40 for most runs; 240 frames and more needed an A100 (80 GB).</li>
            <li>✓ = worked, <span class="text-red-600">✗</span> = failed, – = not run. Green = the two best results; darker is better.</li>
        </ul>

        <h3 class="mt-12 text-xl font-bold font-sans">Failures: Bundaran HI and Monas</h3>

        <p class="mt-4">
            Every run on these two scenes failed. Some examples:
        </p>

        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
            <figure>
                <model-viewer src="{{ asset('riset/vggt-historical-site-reconstruction/hi-30.glb') }}"
                    alt="Failed reconstruction of Bundaran HI, plain, 30 frames"
                    camera-controls auto-rotate loading="lazy" interaction-prompt="auto" interaction-prompt-threshold="1500" class="w-full h-80 bg-gray-100"></model-viewer>
                <figcaption class="mt-2 text-base font-sans text-gray-600">Bundaran HI, plain, 30 frames</figcaption>
            </figure>
            <figure>
                <model-viewer src="{{ asset('riset/vggt-historical-site-reconstruction/hi-n-120.glb') }}"
                    alt="Failed reconstruction of Bundaran HI, color normalized, 120 frames"
                    camera-controls auto-rotate loading="lazy" interaction-prompt="auto" interaction-prompt-threshold="1500" class="w-full h-80 bg-gray-100"></model-viewer>
                <figcaption class="mt-2 text-base font-sans text-gray-600">Bundaran HI, color normalized, 120 frames</figcaption>
            </figure>
            <figure>
                <model-viewer src="{{ asset('riset/vggt-historical-site-reconstruction/monas-30.glb') }}"
                    alt="Failed reconstruction of Monas, plain, 30 frames"
                    camera-controls auto-rotate loading="lazy" interaction-prompt="auto" interaction-prompt-threshold="1500" class="w-full h-80 bg-gray-100"></model-viewer>
                <figcaption class="mt-2 text-base font-sans text-gray-600">Monas, plain, 30 frames</figcaption>
            </figure>
            <figure>
                <model-viewer src="{{ asset('riset/vggt-historical-site-reconstruction/monas-n-15.glb') }}"
                    alt="Failed reconstruction of Monas, color normalized, 15 frames"
                    camera-controls auto-rotate loading="lazy" interaction-prompt="auto" interaction-prompt-threshold="1500" class="w-full h-80 bg-gray-100"></model-viewer>
                <figcaption class="mt-2 text-base font-sans text-gray-600">Monas, color normalized, 15 frames</figcaption>
            </figure>
        </div>

        <h3 class="mt-12 text-xl font-bold font-sans">Why It Likely Failed</h3>

        <p class="mt-4">
            These are my guesses, not tested causes. Each one links back to how VGGT works.
        </p>

        <ul class="mt-4 list-disc pl-6 space-y-4">
            <li>
                <strong>Most of the frame is sky and empty plaza.</strong>
                Sky has no texture and no parallax, so it cannot be matched across photos. The monument covers only a small part of each frame.
                Removing the sky before VGGT did not help either, in every Monas variant,
                so the sky alone does not explain the failure.
            </li>
            <li>
                <strong>Little parallax relative to the distance.</strong>
                From \(Z = fB/d\): for a far subject, the shift \(d\) between photos is small unless the camera moves a lot.
                Walking around a monument that is very tall and far away gives weak parallax on its upper part.
            </li>
            <li>
                <strong>Plain, smooth surfaces.</strong>
                Large uniform surfaces, like the obelisk shaft, look the same from many angles, which makes matching ambiguous.
            </li>
            <li>
                <strong>External photos break the assumptions.</strong>
                The 59 open photos come from different cameras, lenses, times of day, and edits.
                VGGT has no lens distortion model, and histogram matching can align overall color but not lighting direction, shadows, or geometry.
            </li>
        </ul>

        <h3 class="mt-12 text-xl font-bold font-sans">Why Diponegoro Worked</h3>

        <p class="mt-4">
            The statue is close, fills most of the frame, has rich texture, and the walk around it creates strong parallax.
            The best run used only 15 frames, inside the 2 to 24 range VGGT was trained on.
        </p>
    </div>


@verbatim
    <div class="max-w-7xl mx-auto">
        <!-- ================= GLOSSARY ================= -->
        <h3 class="mt-16 text-xl font-bold font-sans">Symbol Glossary</h3>

        <table class="mt-4 w-full border text-base font-sans">
            <tr class="bg-gray-100">
                <th class="border p-2 text-left w-1/4">Symbol</th>
                <th class="border p-2 text-left">Meaning</th>
            </tr>

            <tr class="bg-gray-50"><td class="border p-2 font-bold" colspan="2">Sizes and indices</td></tr>
            <tr><td class="border p-2">\(N\)</td><td class="border p-2">number of photos</td></tr>
            <tr><td class="border p-2">\(i\)</td><td class="border p-2">photo index, \(1 \dots N\). Photo 1 is the reference</td></tr>
            <tr><td class="border p-2">\(j\)</td><td class="border p-2">3D point index (bundle adjustment) or query point index (tracking)</td></tr>
            <tr><td class="border p-2">\(H, W\)</td><td class="border p-2">image height and width, in pixels</td></tr>
            <tr><td class="border p-2">\(m\)</td><td class="border p-2">patch tokens per photo (depends on image size)</td></tr>
            <tr><td class="border p-2">\(n\)</td><td class="border p-2">all tokens per photo: \(n = 1 + 4 + m\)</td></tr>
            <tr><td class="border p-2">\(C\)</td><td class="border p-2">channels: how many numbers make up one token or one feature vector</td></tr>
            <tr><td class="border p-2">\(L\)</td><td class="border p-2">number of backbone layers (24)</td></tr>
            <tr><td class="border p-2">\(M\)</td><td class="border p-2">number of query points in the track loss</td></tr>

            <tr class="bg-gray-50"><td class="border p-2 font-bold" colspan="2">Points and pixels</td></tr>
            <tr><td class="border p-2">\(I_i\)</td><td class="border p-2">photo \(i\), shape \(3 \times H \times W\) (RGB)</td></tr>
            <tr><td class="border p-2">\((u, v)\)</td><td class="border p-2">pixel position: column, row. Origin at the top-left corner</td></tr>
            <tr><td class="border p-2">\(\mathbf{X}\)</td><td class="border p-2">a 3D point \((x, y, z)\)</td></tr>
            <tr><td class="border p-2">\(\mathbf{X}_{\text{cam}}\)</td><td class="border p-2">a 3D point relative to one camera; \(z\) is how far in front of it</td></tr>
            <tr><td class="border p-2">\(\mathbf{X}_{\text{world}}\)</td><td class="border p-2">a 3D point in the shared frame (photo 1's camera)</td></tr>
            <tr><td class="border p-2">\(\mathbf{x}\), \(\mathbf{x}_{ij}\)</td><td class="border p-2">a 2D pixel position; \(\mathbf{x}_{ij}\) is where point \(j\) was found in photo \(i\)</td></tr>
            <tr><td class="border p-2">\(\mathcal{X}\)</td><td class="border p-2">set of all true 3D points of a scene (used for normalization)</td></tr>

            <tr class="bg-gray-50"><td class="border p-2 font-bold" colspan="2">Camera</td></tr>
            <tr><td class="border p-2">\(\mathbf{g}_i\)</td><td class="border p-2">camera of photo \(i\): 9 numbers \([\mathbf{q}_i, \mathbf{t}_i, \mathbf{f}_i]\)</td></tr>
            <tr><td class="border p-2">\(\mathbf{q}\)</td><td class="border p-2">rotation as a quaternion \((x, y, z, w)\), 4 numbers</td></tr>
            <tr><td class="border p-2">\(R\)</td><td class="border p-2">rotation as a 3×3 matrix, built from \(\mathbf{q}\)</td></tr>
            <tr><td class="border p-2">\(R^{\top}\)</td><td class="border p-2">transpose of \(R\); for a rotation it equals the inverse (undoes the rotation)</td></tr>
            <tr><td class="border p-2">\(\mathbf{t}\)</td><td class="border p-2">translation, 3 numbers. \(\mathbf{X}_{\text{cam}} = R\mathbf{X} + \mathbf{t}\)</td></tr>
            <tr><td class="border p-2">\(\mathbf{f} = (\theta_w, \theta_h)\)</td><td class="border p-2">field of view: how wide the lens sees, horizontally and vertically</td></tr>
            <tr><td class="border p-2">\(f\), \(f_x, f_y\)</td><td class="border p-2">focal length in pixels (how zoomed in). Large \(f\) = narrow view</td></tr>
            <tr><td class="border p-2">\(c_x, c_y\)</td><td class="border p-2">principal point; VGGT assumes the image center \((W/2, H/2)\)</td></tr>
            <tr><td class="border p-2">\(K\), \(K_i\)</td><td class="border p-2">lens matrix (intrinsics), built from \(f_x, f_y, c_x, c_y\). Turns a camera-relative point into pixels</td></tr>
            <tr><td class="border p-2">\(K^{-1}\)</td><td class="border p-2">inverse of \(K\). Turns a pixel into a viewing direction</td></tr>
            <tr><td class="border p-2">\(\pi\)</td><td class="border p-2">perspective division: divide by depth \(z\), so far things appear smaller</td></tr>

            <tr class="bg-gray-50"><td class="border p-2 font-bold" colspan="2">Parallax</td></tr>
            <tr><td class="border p-2">\(Z\)</td><td class="border p-2">distance of a point from the camera</td></tr>
            <tr><td class="border p-2">\(B\)</td><td class="border p-2">baseline: distance between two cameras</td></tr>
            <tr><td class="border p-2">\(d\)</td><td class="border p-2">disparity: how far a point shifted between two photos, in pixels</td></tr>

            <tr class="bg-gray-50"><td class="border p-2 font-bold" colspan="2">Network</td></tr>
            <tr><td class="border p-2">\(f(\cdot)\)</td><td class="border p-2">the whole VGGT network, as one function</td></tr>
            <tr><td class="border p-2">\(\mathbf{t}^{I}_i\)</td><td class="border p-2">patch tokens of photo \(i\), from DINO</td></tr>
            <tr><td class="border p-2">\(\mathbf{t}^{g}_i\)</td><td class="border p-2">camera token of photo \(i\)</td></tr>
            <tr><td class="border p-2">\(\mathbf{t}^{R}_i\)</td><td class="border p-2">the 4 register tokens of photo \(i\)</td></tr>
            <tr><td class="border p-2">\(\mathbf{X}_i\) (in attention)</td><td class="border p-2">all \(n\) tokens of photo \(i\), stacked: shape \(n \times C\)</td></tr>
            <tr><td class="border p-2">\(Q, K, V\) (in attention)</td><td class="border p-2">query, key, value: what a token looks for, what it offers, what it passes on. This \(K\) is not the lens matrix</td></tr>
            <tr><td class="border p-2">\(W_Q, W_K, W_V\)</td><td class="border p-2">learned matrices that make \(Q, K, V\) from the tokens</td></tr>
            <tr><td class="border p-2">\(F_i\)</td><td class="border p-2">full-resolution feature map inside the dense head</td></tr>

            <tr class="bg-gray-50"><td class="border p-2 font-bold" colspan="2">Outputs</td></tr>
            <tr><td class="border p-2">\(D_i\)</td><td class="border p-2">depth map: per pixel, how far in front of the camera (along \(z\))</td></tr>
            <tr><td class="border p-2">\(P_i\)</td><td class="border p-2">point map: per pixel, 3D position in photo 1's frame</td></tr>
            <tr><td class="border p-2">\(\Sigma^{D}_i, \Sigma^{P}_i\)</td><td class="border p-2">per-pixel confidence for the depth map and the point map</td></tr>
            <tr><td class="border p-2">\(T_i\)</td><td class="border p-2">tracking features: one feature vector per pixel</td></tr>
            <tr><td class="border p-2">\(\mathbf{y}_q\)</td><td class="border p-2">query pixel chosen for tracking</td></tr>
            <tr><td class="border p-2">\(\mathbf{h}\)</td><td class="border p-2">feature read from \(T_q\) at \(\mathbf{y}_q\)</td></tr>
            <tr><td class="border p-2">\(\mathbf{y}_{j,i}\)</td><td class="border p-2">2D position of query point \(j\) in photo \(i\)</td></tr>
            <tr><td class="border p-2">\(\tau\)</td><td class="border p-2">confidence threshold for keeping a point</td></tr>

            <tr class="bg-gray-50"><td class="border p-2 font-bold" colspan="2">Loss</td></tr>
            <tr><td class="border p-2">\(\mathcal{L}\)</td><td class="border p-2">total loss: how wrong the predictions are</td></tr>
            <tr><td class="border p-2">\(\lambda\)</td><td class="border p-2">weight of the track loss (0.05)</td></tr>
            <tr><td class="border p-2">\(\alpha\)</td><td class="border p-2">weight of the uncertainty cost in the depth and point-map loss</td></tr>
            <tr><td class="border p-2">\(\rho\), \(\rho_\epsilon\), \(\|\cdot\|_\epsilon\)</td><td class="border p-2">robust function; here the Huber loss with threshold \(\epsilon\)</td></tr>
            <tr><td class="border p-2">\(s\)</td><td class="border p-2">scale factor: average distance of the true 3D points from the origin</td></tr>

            <tr class="bg-gray-50"><td class="border p-2 font-bold" colspan="2">Notation</td></tr>
            <tr><td class="border p-2">\(\hat{\ }\) (hat)</td><td class="border p-2">a prediction. \(\hat{D}\) = predicted depth, \(D\) = true depth</td></tr>
            <tr><td class="border p-2">\(\mathbb{R}^{a \times b}\)</td><td class="border p-2">a grid of real numbers with shape \(a \times b\)</td></tr>
            <tr><td class="border p-2">\(\|\cdot\|\)</td><td class="border p-2">length (size) of a vector, i.e. distance</td></tr>
            <tr><td class="border p-2">\(\odot\)</td><td class="border p-2">multiply element by element (pixel by pixel)</td></tr>
            <tr><td class="border p-2">\(\nabla\)</td><td class="border p-2">gradient: change between neighboring pixels</td></tr>
            <tr><td class="border p-2">\(^{\top}\)</td><td class="border p-2">transpose: swap rows and columns</td></tr>
            <tr><td class="border p-2">\(\leftarrow\)</td><td class="border p-2">replace the left side with the right side</td></tr>
        </table>

        <!-- ================= REFERENCES ================= -->
        <h3 class="mt-12 text-xl font-bold font-sans">References</h3>

        <ul class="mt-4 list-disc pl-6 space-y-1">
            <li><a href="https://arxiv.org/abs/2503.11651" class="underline">Wang et al., "VGGT: Visual Geometry Grounded Transformer", CVPR 2025</a></li>
            <li><a href="https://vgg-t.github.io" class="underline">Project page</a></li>
            <li><a href="https://github.com/facebookresearch/vggt" class="underline">VGGT source code</a></li>
            <li><a href="https://github.com/alphasuthamedia/vggt" class="underline">This experiment: videos, sampled frames, external photos, color normalization, sky removal, and the VGGT-to-mesh pipeline</a></li>
        </ul>
    </div>
@endverbatim

</div>
@endsection