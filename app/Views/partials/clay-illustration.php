<svg class="clay-scene" viewBox="0 0 400 520" preserveAspectRatio="xMidYMid slice" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Illustration of soft clay hills, a sun, plants, and Montessori building blocks">
    <defs>
        <linearGradient id="gradSky" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="#F7F2E7"/>
            <stop offset="100%" stop-color="#E7DECC"/>
        </linearGradient>
        <linearGradient id="gradSun" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0%" stop-color="#E7A87C"/>
            <stop offset="100%" stop-color="#C97744"/>
        </linearGradient>
        <linearGradient id="gradHillBack" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0%" stop-color="#9CBBCF"/>
            <stop offset="100%" stop-color="#6E93AC"/>
        </linearGradient>
        <linearGradient id="gradHillMid" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0%" stop-color="#8FAD8A"/>
            <stop offset="100%" stop-color="#658362"/>
        </linearGradient>
        <linearGradient id="gradHillFront" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0%" stop-color="#7FA07A"/>
            <stop offset="100%" stop-color="#577553"/>
        </linearGradient>
        <linearGradient id="gradLeaf" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0%" stop-color="#8AAB84"/>
            <stop offset="100%" stop-color="#5C7C57"/>
        </linearGradient>
        <linearGradient id="gradCloud" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="#FFFFFF"/>
            <stop offset="100%" stop-color="#E9E1D0"/>
        </linearGradient>
        <linearGradient id="gradBlockBlue" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0%" stop-color="#9AC0DA"/>
            <stop offset="100%" stop-color="#6F97B3"/>
        </linearGradient>
        <linearGradient id="gradBlockTerra" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0%" stop-color="#E3A57E"/>
            <stop offset="100%" stop-color="#C07E52"/>
        </linearGradient>
        <linearGradient id="gradBlockCream" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0%" stop-color="#FBF7EE"/>
            <stop offset="100%" stop-color="#E4DCC9"/>
        </linearGradient>
        <filter id="claySoft" x="-40%" y="-40%" width="180%" height="180%">
            <feDropShadow dx="0" dy="6" stdDeviation="6" flood-color="#3B362C" flood-opacity="0.22"/>
        </filter>
    </defs>

    <rect x="0" y="0" width="400" height="520" fill="url(#gradSky)"/>

    <!-- drifting cloud blobs, filling the upper sky so it doesn't read as empty -->
    <g opacity="0.9" filter="url(#claySoft)">
        <ellipse cx="95" cy="90" rx="42" ry="16" fill="url(#gradCloud)"/>
        <ellipse cx="125" cy="80" rx="28" ry="14" fill="url(#gradCloud)"/>
    </g>
    <g opacity="0.75" filter="url(#claySoft)">
        <ellipse cx="330" cy="185" rx="34" ry="13" fill="url(#gradCloud)"/>
        <ellipse cx="355" cy="178" rx="20" ry="11" fill="url(#gradCloud)"/>
    </g>

    <!-- sun -->
    <circle cx="260" cy="105" r="50" fill="url(#gradSun)" filter="url(#claySoft)"/>
    <ellipse cx="242" cy="88" rx="15" ry="10" fill="#FBF0E3" opacity="0.55"/>

    <!-- back hill (raised higher to fill more of the frame) -->
    <path d="M0 240 C 70 190, 160 190, 220 230 C 280 270, 340 220, 400 240 L400 520 L0 520 Z"
          fill="url(#gradHillBack)" filter="url(#claySoft)"/>

    <!-- mid hill -->
    <path d="M0 310 C 90 260, 190 260, 250 300 C 310 340, 360 300, 400 315 L400 520 L0 520 Z"
          fill="url(#gradHillMid)" filter="url(#claySoft)"/>

    <!-- front hill -->
    <path d="M0 375 C 100 335, 210 345, 280 385 C 330 410, 370 390, 400 395 L400 520 L0 520 Z"
          fill="url(#gradHillFront)"/>

    <!-- leaf motifs -->
    <g filter="url(#claySoft)">
        <path d="M75 350 C 60 320, 70 285, 100 265 C 120 295, 115 330, 75 350 Z" fill="url(#gradLeaf)"/>
        <path d="M75 350 C 90 330, 95 300, 100 265 C 105 300, 105 330, 75 350 Z" fill="#4E6B4A" opacity="0.5"/>
    </g>
    <g filter="url(#claySoft)">
        <path d="M325 390 C 313 365, 321 338, 345 322 C 361 346, 357 374, 325 390 Z" fill="url(#gradLeaf)"/>
        <path d="M325 390 C 337 372, 341 348, 345 322 C 349 350, 349 374, 325 390 Z" fill="#4E6B4A" opacity="0.5"/>
    </g>
    <g filter="url(#claySoft)">
        <path d="M180 300 C 170 280, 176 258, 196 245 C 209 265, 205 288, 180 300 Z" fill="url(#gradLeaf)"/>
    </g>

    <!-- montessori-style blocks -->
    <g filter="url(#claySoft)" transform="translate(150 415) rotate(-6)">
        <rect x="0" y="0" width="48" height="48" rx="12" fill="url(#gradBlockTerra)"/>
    </g>
    <g filter="url(#claySoft)" transform="translate(198 432) rotate(4)">
        <rect x="0" y="0" width="36" height="36" rx="10" fill="url(#gradBlockBlue)"/>
    </g>
    <g filter="url(#claySoft)" transform="translate(118 448) rotate(3)">
        <rect x="0" y="0" width="28" height="28" rx="8" fill="url(#gradBlockCream)"/>
    </g>
    <g filter="url(#claySoft)" transform="translate(250 455) rotate(-8)">
        <rect x="0" y="0" width="24" height="24" rx="7" fill="url(#gradBlockTerra)"/>
    </g>

    <!-- floating dots, scattered through the mid-sky for texture -->
    <circle cx="110" cy="220" r="5" fill="#C97744" opacity="0.5"/>
    <circle cx="150" cy="150" r="3.5" fill="#7FA07A" opacity="0.5"/>
    <circle cx="60" cy="180" r="4" fill="#6F93AC" opacity="0.45"/>
    <circle cx="340" cy="260" r="4.5" fill="#7FA07A" opacity="0.45"/>
</svg>
