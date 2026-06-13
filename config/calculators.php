<?php

/*
|--------------------------------------------------------------------------
| Calculators — single source of truth
|--------------------------------------------------------------------------
| Drives the /calculators hub cards, each tool's SEO meta, the on-page
| how-to guide, the FAQ block (also emitted as FAQPage JSON-LD), and the
| related-peptide bridge. Each entry maps to a widget partial at
| resources/views/calculators/widgets/{slug}.blade.php.
|
| Keep copy research/educational — these tools support peptide *research*,
| not personalized medical dosing. Every page renders the shared disclaimer.
*/

return [

    // ----------------------------------------------------------------------
    'reconstitution' => [
        'slug'        => 'reconstitution',
        'name'        => 'Reconstitution Calculator',
        'short'       => 'Reconstitution',
        'emoji'       => '💉',
        'tagline'     => 'Calculate BAC water, concentration and exact syringe units for any peptide.',
        'category'    => 'Peptide',
        'accent'      => '#2563EB',
        'seo_title'   => 'Peptide Reconstitution Calculator — BAC Water & Syringe Units',
        'seo_description' => 'Free peptide reconstitution calculator. Enter vial mg, bacteriostatic water mL and your target dose to get concentration and the exact units to draw on an insulin syringe.',
        'intro'       => 'Reconstitution is the step that turns a vial of lyophilized (freeze-dried) peptide into an accurately measurable solution. Get the water-to-peptide ratio right and every dose afterwards is simple and repeatable. This calculator does the math for you: enter how much peptide is in the vial, how much bacteriostatic water you are adding, and your target dose — it returns the concentration and the precise number of units to draw on a U-100 insulin syringe.',
        'how_to'      => [
            ['title' => 'Enter the peptide amount', 'body' => 'Type the total milligrams of peptide printed on the vial label (e.g. 5 mg, 10 mg). This is the dry powder amount before any water is added.'],
            ['title' => 'Set the bacteriostatic water volume', 'body' => 'Enter how many millilitres of bacteriostatic (BAC) water you will add. More water makes small doses easier to measure accurately; 1–3 mL is typical.'],
            ['title' => 'Enter your target dose', 'body' => 'Enter the dose you want per injection in micrograms (mcg) or milligrams (mg). The tool converts everything to a single concentration internally.'],
            ['title' => 'Read your syringe units', 'body' => 'The result shows mcg/mL concentration and exactly how many units to draw on a 100-unit insulin syringe, plus the draw volume in mL.'],
        ],
        'faqs'        => [
            ['q' => 'How do you calculate peptide reconstitution?', 'a' => 'Concentration (mcg/mL) = peptide amount in mg × 1000 ÷ water volume in mL. To find units on a U-100 syringe, divide your dose (mcg) by (concentration ÷ 100). This calculator runs both steps automatically.'],
            ['q' => 'What is bacteriostatic water and why use it?', 'a' => 'Bacteriostatic water is sterile water containing 0.9% benzyl alcohol, which inhibits bacterial growth so a multi-use vial stays usable for weeks. It is the standard diluent for reconstituting peptides in research settings.'],
            ['q' => 'How much water should I add to a peptide vial?', 'a' => 'There is no single correct amount — more water simply dilutes the peptide so each unit on the syringe holds less. Choose a volume that puts your target dose at an easy-to-read number of units (e.g. 10–30 units). The calculator lets you test different volumes instantly.'],
            ['q' => 'What does “units” mean on an insulin syringe?', 'a' => 'A U-100 insulin syringe is marked in 100 units per mL, so 1 unit = 0.01 mL. The calculator converts your dose into units so you can draw it directly without separate volume math.'],
        ],
        'related'     => ['bpc-157', 'tb-500', 'semaglutide', 'ipamorelin'],
    ],

    // ----------------------------------------------------------------------
    'bmi' => [
        'slug'        => 'bmi',
        'name'        => 'BMI Calculator',
        'short'       => 'BMI',
        'emoji'       => '📊',
        'tagline'     => 'Calculate Body Mass Index and see your healthy-weight range.',
        'category'    => 'Body',
        'accent'      => '#0EA5E9',
        'seo_title'   => 'BMI Calculator — Body Mass Index, Category & Healthy Range',
        'seo_description' => 'Free BMI calculator. Enter your height and weight in metric or imperial units to get your Body Mass Index, weight category, and the healthy-weight range for your height.',
        'intro'       => 'Body Mass Index (BMI) is a quick screening number that relates your weight to your height. It is widely used as a baseline metric and as a starting point for tracking changes over a weight-management protocol. Enter your height and weight in either metric or imperial units and this calculator returns your BMI, the standard category it falls into, and the weight range considered healthy for your height.',
        'how_to'      => [
            ['title' => 'Choose your units', 'body' => 'Switch between metric (cm / kg) and imperial (ft-in / lb). The calculator converts internally so the result is identical either way.'],
            ['title' => 'Enter height and weight', 'body' => 'Type your height and current weight. Values update the result instantly as you type.'],
            ['title' => 'Read your BMI and category', 'body' => 'The result shows your BMI to one decimal place and the World Health Organization category: underweight, healthy, overweight, or obese.'],
            ['title' => 'See your healthy range', 'body' => 'The tool also shows the weight range that corresponds to a healthy BMI (18.5–24.9) for your exact height, so you have a concrete target.'],
        ],
        'faqs'        => [
            ['q' => 'How is BMI calculated?', 'a' => 'BMI = weight (kg) ÷ height (m)². For imperial units the formula is weight (lb) ÷ height (in)² × 703. This calculator applies the correct formula automatically based on the units you choose.'],
            ['q' => 'What is a healthy BMI range?', 'a' => 'The standard categories are: under 18.5 underweight, 18.5–24.9 healthy, 25–29.9 overweight, and 30 or above obese. These are population screening bands, not a diagnosis.'],
            ['q' => 'Is BMI accurate for everyone?', 'a' => 'BMI does not distinguish muscle from fat, so very muscular people can read as “overweight” while having low body fat. It is a useful population screen and progress tracker, not a complete measure of health.'],
            ['q' => 'How does BMI relate to GLP-1 protocols?', 'a' => 'BMI is a common eligibility and progress metric in weight-management research. Tracking BMI alongside a GLP-1 titration shows trend over time — see the GLP-1 calculator for a projected timeline.'],
        ],
        'related'     => ['semaglutide', 'tirzepatide', 'retatrutide'],
    ],

    // ----------------------------------------------------------------------
    'glp-1' => [
        'slug'        => 'glp-1',
        'name'        => 'GLP-1 Calculator',
        'short'       => 'GLP-1',
        'emoji'       => '🔬',
        'tagline'     => 'Model a GLP-1 titration schedule and projected weight-loss timeline.',
        'category'    => 'Peptide',
        'accent'      => '#7C3AED',
        'seo_title'   => 'GLP-1 Calculator — Titration Schedule & Weight-Loss Timeline',
        'seo_description' => 'Free GLP-1 calculator for semaglutide and tirzepatide research. Model a standard titration schedule and an educational projected weight-loss timeline with BMI tracking.',
        'intro'       => 'GLP-1 receptor agonists such as semaglutide and tirzepatide are typically introduced with a gradual “titration” — starting low and stepping the dose up over several weeks to improve tolerability. This educational calculator lays out a standard titration ladder for the compound you select and projects an illustrative weight-loss trajectory based on average percentages reported in the clinical literature, with your BMI tracked at each milestone. It is a planning and learning aid, not medical advice.',
        'how_to'      => [
            ['title' => 'Select the compound', 'body' => 'Choose semaglutide or tirzepatide. Each has its own standard titration ladder and typical maintenance dose range.'],
            ['title' => 'Enter your starting stats', 'body' => 'Provide your height and current weight so the tool can compute starting BMI and translate percentage changes into actual weight.'],
            ['title' => 'Review the titration schedule', 'body' => 'The calculator shows the week-by-week dose steps from the starting dose up to the maintenance range.'],
            ['title' => 'See the projected timeline', 'body' => 'An illustrative trajectory shows estimated weight and BMI at 4, 12, 24, 52 and 68 weeks using average published response rates. Individual results vary widely.'],
        ],
        'faqs'        => [
            ['q' => 'How does GLP-1 titration work?', 'a' => 'Titration means starting at a low dose and increasing it on a fixed schedule (usually every 4 weeks) until reaching a maintenance dose. Stepping up gradually reduces gastrointestinal side effects. The exact ladder depends on the specific compound.'],
            ['q' => 'How much weight is lost on semaglutide vs tirzepatide?', 'a' => 'In published trials, average weight reduction at around 68 weeks was roughly 15% of body weight for semaglutide (STEP) and roughly 20%+ for tirzepatide (SURMOUNT) at higher doses. These are study averages; individual outcomes differ.'],
            ['q' => 'Is this calculator medical advice?', 'a' => 'No. It is an educational model that illustrates standard schedules and average literature outcomes. It does not account for your medical history and is not a substitute for a qualified healthcare professional.'],
            ['q' => 'How do I reconstitute GLP-1 peptides?', 'a' => 'Lyophilized GLP-1 research peptides are reconstituted with bacteriostatic water like any other peptide — use the reconstitution calculator to convert your vial size and dose into syringe units.'],
        ],
        'related'     => ['semaglutide', 'tirzepatide', 'retatrutide', 'cagrilintide'],
    ],

    // ----------------------------------------------------------------------
    'trt' => [
        'slug'        => 'trt',
        'name'        => 'TRT Calculator',
        'short'       => 'TRT',
        'emoji'       => '💪',
        'tagline'     => 'Convert a weekly testosterone target into per-injection volume and cadence.',
        'category'    => 'Hormone',
        'accent'      => '#DC2626',
        'seo_title'   => 'TRT Calculator — Testosterone Dose, Volume & Injection Frequency',
        'seo_description' => 'Educational TRT calculator. Convert a weekly testosterone target and ester concentration into per-injection volume and a once/twice/EOD frequency split. Research and information only.',
        'intro'       => 'Testosterone replacement is usually prescribed as a weekly milligram target delivered by an oil-based ester (for example testosterone cypionate or enanthate at 200 mg/mL). This calculator converts a weekly target and your vial concentration into the volume per injection and shows how that splits across common cadences (once weekly, twice weekly, or every other day). Testosterone is a controlled substance; this tool is strictly informational and is not medical advice or a recommendation to use it.',
        'how_to'      => [
            ['title' => 'Enter the weekly target', 'body' => 'Type the total weekly testosterone target in milligrams. Common clinical ranges are shown for reference only.'],
            ['title' => 'Set the ester concentration', 'body' => 'Enter the concentration of your preparation in mg/mL (200 mg/mL is the most common for cypionate/enanthate).'],
            ['title' => 'Choose an injection frequency', 'body' => 'Pick once weekly, twice weekly, or every other day. More frequent injections mean a smaller, steadier volume each time.'],
            ['title' => 'Read volume and units', 'body' => 'The result shows the volume per injection in mL and the equivalent units on a U-100 syringe for each cadence.'],
        ],
        'faqs'        => [
            ['q' => 'How do you calculate a testosterone injection volume?', 'a' => 'Volume per week (mL) = weekly dose (mg) ÷ concentration (mg/mL). Divide that by the number of injections per week to get the per-injection volume, then multiply by 100 for U-100 syringe units.'],
            ['q' => 'How often should testosterone be injected?', 'a' => 'Long esters like cypionate and enanthate are commonly split into once- or twice-weekly injections; some protocols use every-other-day for steadier levels. Frequency is a clinical decision made with a prescriber.'],
            ['q' => 'Is this TRT calculator medical advice?', 'a' => 'No. Testosterone is a Schedule III controlled substance. This calculator is an arithmetic aid for educational purposes only and must not be used to self-medicate. Hormone therapy requires a licensed physician and bloodwork.'],
            ['q' => 'What does mg/mL mean on a testosterone vial?', 'a' => 'It is the concentration — how many milligrams of testosterone are dissolved in each millilitre of oil. 200 mg/mL means 1 mL contains 200 mg.'],
        ],
        'related'     => ['tesamorelin', 'ipamorelin', 'cjc-1295-dac'],
    ],

    // ----------------------------------------------------------------------
    'melanotan' => [
        'slug'        => 'melanotan',
        'name'        => 'Melanotan Calculator',
        'short'       => 'Melanotan',
        'emoji'       => '☀️',
        'tagline'     => 'Reconstitute MT-II and map a loading and maintenance schedule.',
        'category'    => 'Peptide',
        'accent'      => '#EA580C',
        'seo_title'   => 'Melanotan II (MT-II) Calculator — Reconstitution & Dosing Schedule',
        'seo_description' => 'Educational Melanotan II calculator. Reconstitute MT-II, convert micro-doses into syringe units, and map a typical loading-then-maintenance schedule. Research information only.',
        'intro'       => 'Melanotan II (MT-II) is a melanocortin peptide studied for its effect on melanogenesis (skin pigmentation). It is supplied as a lyophilized powder and reconstituted with bacteriostatic water like other peptides, but it is typically used at very small micro-doses, which makes accurate syringe-unit conversion important. This calculator reconstitutes your vial, converts a micro-dose into units, and illustrates a common loading-then-maintenance pattern. Educational use only.',
        'how_to'      => [
            ['title' => 'Reconstitute the vial', 'body' => 'Enter the MT-II amount in the vial (commonly 10 mg) and the bacteriostatic water volume you are adding.'],
            ['title' => 'Set your micro-dose', 'body' => 'Enter the dose in micrograms. MT-II is used in small mcg amounts, so the unit conversion is what keeps it accurate.'],
            ['title' => 'Read the units to draw', 'body' => 'The result shows the concentration and the exact units on a U-100 syringe for your micro-dose.'],
            ['title' => 'Review the schedule pattern', 'body' => 'An illustrative loading phase (building up) followed by a maintenance phase (occasional top-ups) is shown for reference.'],
        ],
        'faqs'        => [
            ['q' => 'How do you reconstitute Melanotan II?', 'a' => 'Add bacteriostatic water to the lyophilized MT-II vial and let it dissolve gently. Concentration (mcg/mL) = mg in vial × 1000 ÷ water mL. This calculator converts that into syringe units for your micro-dose.'],
            ['q' => 'Why is MT-II dosed in such small amounts?', 'a' => 'MT-II is potent at the micro-dose level, so doses are measured in micrograms. Adding a known water volume and converting to units prevents over-drawing.'],
            ['q' => 'What is a loading vs maintenance phase?', 'a' => 'A loading phase uses smaller, more frequent doses to build an effect gradually; a maintenance phase uses occasional doses to sustain it. The pattern shown here is illustrative only.'],
            ['q' => 'Is Melanotan II approved for use?', 'a' => 'Melanotan II is not an approved medicine in most jurisdictions and is sold for research only. This calculator is educational and is not a recommendation to use it.'],
        ],
        'related'     => ['melanotan-ii', 'pt-141'],
    ],

    // ----------------------------------------------------------------------
    'fitness' => [
        'slug'        => 'fitness',
        'name'        => 'Fitness Calculator',
        'short'       => 'Fitness',
        'emoji'       => '🏃',
        'tagline'     => 'Calculate BMR, TDEE, calorie targets and a macro split.',
        'category'    => 'Body',
        'accent'      => '#059669',
        'seo_title'   => 'Fitness Calculator — BMR, TDEE, Calories & Macros',
        'seo_description' => 'Free fitness calculator. Get your BMR (Mifflin–St Jeor), TDEE by activity level, calorie targets for cut/maintain/bulk, and a protein-carb-fat macro split.',
        'intro'       => 'Your Basal Metabolic Rate (BMR) is the energy your body uses at rest; your Total Daily Energy Expenditure (TDEE) is BMR scaled by how active you are. Together they set the calorie baseline for any body-composition goal. This calculator uses the Mifflin–St Jeor equation — the modern standard — to estimate BMR and TDEE, then suggests calorie targets for cutting, maintaining or bulking and a sensible macronutrient split.',
        'how_to'      => [
            ['title' => 'Enter your stats', 'body' => 'Provide age, sex, height and weight in metric or imperial units.'],
            ['title' => 'Pick your activity level', 'body' => 'Choose from sedentary up to extremely active. This multiplier turns BMR into TDEE.'],
            ['title' => 'Choose a goal', 'body' => 'Select cut, maintain or bulk. The tool applies a standard calorie deficit or surplus to your TDEE.'],
            ['title' => 'Read calories and macros', 'body' => 'See your BMR, TDEE, daily calorie target and a protein/carb/fat breakdown in grams.'],
        ],
        'faqs'        => [
            ['q' => 'What is the Mifflin–St Jeor equation?', 'a' => 'It estimates BMR as 10 × weight(kg) + 6.25 × height(cm) − 5 × age + 5 (men) or − 161 (women). It is considered more accurate for modern populations than the older Harris–Benedict formula.'],
            ['q' => 'What is the difference between BMR and TDEE?', 'a' => 'BMR is the calories you burn at complete rest. TDEE is BMR multiplied by an activity factor (1.2 sedentary to ~1.9 extremely active) and represents your real daily energy needs.'],
            ['q' => 'How many calories to lose or gain weight?', 'a' => 'A deficit of about 15–20% below TDEE supports fat loss; a surplus of about 10–15% supports muscle gain. This calculator applies those standard adjustments automatically.'],
            ['q' => 'How much protein should I eat?', 'a' => 'A common target is roughly 1.6–2.2 g of protein per kg of body weight for active people. The macro split here uses a protein-forward default you can use as a starting point.'],
        ],
        'related'     => ['tesamorelin', 'mots-c', 'ipamorelin'],
    ],

    // ----------------------------------------------------------------------
    'protocol' => [
        'slug'        => 'protocol',
        'name'        => 'Protocol Tool',
        'short'       => 'Protocol',
        'emoji'       => '🌐',
        'tagline'     => 'Plan a multi-peptide protocol with reconstitution and weekly timing.',
        'category'    => 'Peptide',
        'accent'      => '#0D9488',
        'seo_title'   => 'Peptide Protocol Tool — Stack Planner, Reconstitution & Timing',
        'seo_description' => 'Free peptide protocol planner. Select peptides, set vial sizes and doses, and build a weekly schedule with reconstitution units and an AM/PM timing grid. Research use only.',
        'intro'       => 'Running more than one peptide means tracking several vials, concentrations, doses and injection times at once. This protocol tool lets you assemble a research plan: pick the peptides you are studying, enter each vial size and target dose, and it returns the reconstitution units for each plus a consolidated weekly timing grid. It is a planning and record-keeping aid for educational purposes only.',
        'how_to'      => [
            ['title' => 'Add peptides to your protocol', 'body' => 'Select one or more peptides from the list. Common research stacks (e.g. BPC-157 + TB-500) are quick to assemble.'],
            ['title' => 'Set vial size and dose', 'body' => 'For each peptide, enter the vial mg, the bacteriostatic water you will add, and your target per-injection dose.'],
            ['title' => 'Choose frequency and timing', 'body' => 'Set how many times per week and whether it is taken AM, PM, or both. The tool builds a weekly grid.'],
            ['title' => 'Review your plan', 'body' => 'Each peptide shows its concentration and syringe units, and the weekly grid shows what to take when.'],
        ],
        'faqs'        => [
            ['q' => 'How do I plan a peptide stack?', 'a' => 'List each peptide, reconstitute it (vial mg ÷ water mL), convert your dose to syringe units, then map injection days and times on a weekly grid. This tool automates all of those steps in one place.'],
            ['q' => 'Can I run two peptides at the same time?', 'a' => 'Many research protocols pair complementary peptides (for example BPC-157 with TB-500 for tissue-repair studies). The tool keeps each one’s reconstitution and timing separate so nothing gets mixed up.'],
            ['q' => 'Should peptides be taken in the morning or evening?', 'a' => 'Timing depends on the compound — growth-hormone secretagogues are often taken before bed or fasted, for example. The tool lets you assign AM/PM per peptide so you can record your intended schedule.'],
            ['q' => 'Is this protocol tool medical advice?', 'a' => 'No. It is an educational planner that does the arithmetic and scheduling. It does not recommend compounds, doses or combinations and is not a substitute for professional guidance.'],
        ],
        'related'     => ['bpc-157', 'tb-500', 'ipamorelin', 'cjc-1295-dac'],
    ],

    // ----------------------------------------------------------------------
    'tdee' => [
        'slug'        => 'tdee',
        'name'        => 'TDEE Calculator',
        'short'       => 'TDEE',
        'emoji'       => '🔥',
        'tagline'     => 'Find your Total Daily Energy Expenditure and calorie targets.',
        'category'    => 'Body',
        'accent'      => '#F97316',
        'seo_title'   => 'TDEE Calculator — Total Daily Energy Expenditure & Calories',
        'seo_description' => 'Free TDEE calculator using the Mifflin–St Jeor equation. Find your Total Daily Energy Expenditure, BMR, and calorie targets for fat loss, maintenance and muscle gain.',
        'intro'       => 'Total Daily Energy Expenditure (TDEE) is the number of calories you burn in a day — your Basal Metabolic Rate (the energy used at rest) scaled by how active you are. It is the single most useful number for any nutrition goal: eat below it to lose fat, at it to maintain, above it to gain. This calculator estimates your BMR with the Mifflin–St Jeor equation, applies an activity multiplier, and shows your TDEE plus calorie targets for each goal.',
        'how_to'      => [
            ['title' => 'Enter your stats', 'body' => 'Provide age, sex, height and weight in metric or imperial units.'],
            ['title' => 'Select activity level', 'body' => 'Choose from sedentary to extremely active — this multiplier converts BMR into TDEE.'],
            ['title' => 'Read your TDEE', 'body' => 'See your BMR and TDEE, plus a breakdown of TDEE at every activity level for comparison.'],
            ['title' => 'Pick a calorie target', 'body' => 'Use the suggested fat-loss, maintenance and muscle-gain calorie numbers as your daily intake goal.'],
        ],
        'faqs'        => [
            ['q' => 'What is TDEE?', 'a' => 'TDEE (Total Daily Energy Expenditure) is the total calories you burn per day, including your resting metabolism, daily movement, exercise and the energy used to digest food. It is your real daily calorie need.'],
            ['q' => 'How is TDEE calculated?', 'a' => 'TDEE = BMR × an activity factor. BMR is estimated with the Mifflin–St Jeor equation, then multiplied by 1.2 (sedentary) up to about 1.9 (extremely active) depending on your weekly activity.'],
            ['q' => 'How accurate is a TDEE calculator?', 'a' => 'TDEE estimates are usually within 10–15% for most people. Use the number as a starting point, then adjust based on how your weight actually changes over 2–3 weeks.'],
            ['q' => 'How do I use TDEE to lose weight?', 'a' => 'Eat below your TDEE — a deficit of roughly 15–20% supports steady fat loss. To gain muscle, eat slightly above TDEE. The calculator shows both targets automatically.'],
        ],
        'related'     => ['tesamorelin', 'mots-c', 'ipamorelin', 'semaglutide'],
    ],

    // ----------------------------------------------------------------------
    'body-fat' => [
        'slug'        => 'body-fat',
        'name'        => 'Body Fat Calculator',
        'short'       => 'Body Fat',
        'emoji'       => '📐',
        'tagline'     => 'Estimate body fat percentage with the U.S. Navy method.',
        'category'    => 'Body',
        'accent'      => '#14B8A6',
        'seo_title'   => 'Body Fat Calculator — U.S. Navy Method Body Fat Percentage',
        'seo_description' => 'Free body fat percentage calculator using the U.S. Navy tape method. Enter neck, waist (and hip) measurements to estimate body fat %, fat mass, lean mass and category.',
        'intro'       => 'The U.S. Navy method estimates body fat percentage from simple tape measurements — neck and waist for men, plus hips for women — alongside your height. It is one of the most accessible ways to track body composition at home without callipers or a scan. Enter your measurements and this calculator returns your estimated body fat percentage, the category it falls into, and your fat-mass and lean-mass split.',
        'how_to'      => [
            ['title' => 'Choose sex and units', 'body' => 'Body fat formulas differ by sex; women also include a hip measurement. Switch between metric (cm) and imperial (in).'],
            ['title' => 'Measure neck and waist', 'body' => 'Measure your neck below the larynx and your waist at the navel, relaxed. Women also measure the hips at the widest point.'],
            ['title' => 'Enter height and weight', 'body' => 'Add your height (used by the formula) and weight (used to split fat mass from lean mass).'],
            ['title' => 'Read your body fat %', 'body' => 'See your estimated body fat percentage, category, and the kilograms (or pounds) of fat versus lean mass.'],
        ],
        'faqs'        => [
            ['q' => 'How does the U.S. Navy body fat method work?', 'a' => 'It uses a logarithmic formula based on the circumference of your neck and waist (plus hips for women) relative to your height. The differences between these measurements correlate with body fat.'],
            ['q' => 'How accurate is the Navy body fat calculator?', 'a' => 'The Navy method is typically accurate to within 3–4% of a DEXA scan for most people, which makes it a reliable, free way to track changes over time. Consistent measuring technique matters more than a single reading.'],
            ['q' => 'What is a healthy body fat percentage?', 'a' => 'General ranges: men 10–20% and women 18–28% are commonly considered healthy. Athletes are often lower; essential fat is roughly 3–5% for men and 10–13% for women.'],
            ['q' => 'How do I measure correctly?', 'a' => 'Use a flexible tape, keep it level and snug but not compressing the skin, measure on bare skin, and take each measurement two or three times to average out error.'],
        ],
        'related'     => ['tesamorelin', 'mots-c', 'semaglutide', 'tirzepatide'],
    ],

    // ----------------------------------------------------------------------
    'macro' => [
        'slug'        => 'macro',
        'name'        => 'Macro Calculator',
        'short'       => 'Macros',
        'emoji'       => '🥗',
        'tagline'     => 'Turn a calorie target into protein, carb and fat grams.',
        'category'    => 'Body',
        'accent'      => '#16A34A',
        'seo_title'   => 'Macro Calculator — Protein, Carbs & Fat Grams by Goal',
        'seo_description' => 'Free macro calculator. Enter your daily calories (or TDEE) and goal to get protein, carbohydrate and fat targets in grams for balanced, high-protein, low-carb or keto splits.',
        'intro'       => 'Once you know how many calories to eat, macros decide what those calories are made of — protein, carbohydrate and fat. Getting enough protein protects muscle, while your carb-to-fat balance is mostly preference and performance. This calculator takes your daily calorie target (and body weight, for a protein floor) and splits it into gram targets for the diet style you choose: balanced, high-protein, low-carb or ketogenic.',
        'how_to'      => [
            ['title' => 'Enter your calories', 'body' => 'Type your daily calorie target. Not sure? Use the TDEE calculator first, then bring the number here.'],
            ['title' => 'Add your weight', 'body' => 'Your body weight sets a sensible protein floor (grams of protein per kg or lb).'],
            ['title' => 'Choose a diet style', 'body' => 'Pick balanced, high-protein, low-carb or keto — each shifts the carb-to-fat ratio while keeping protein adequate.'],
            ['title' => 'Read your macros', 'body' => 'See your protein, carbohydrate and fat targets in grams, with the calorie split for each.'],
        ],
        'faqs'        => [
            ['q' => 'What are macros?', 'a' => 'Macronutrients — protein, carbohydrate and fat — are the three nutrients that provide calories. Protein and carbs have about 4 calories per gram, fat has about 9. Your macro split is how your total calories are divided among them.'],
            ['q' => 'How much protein should I eat?', 'a' => 'A common range is 1.6–2.2 g of protein per kg of body weight for active people aiming to build or retain muscle. This calculator sets protein from your weight first, then fills the rest with carbs and fat.'],
            ['q' => 'What is a good macro split?', 'a' => 'A balanced split is roughly 30% protein / 40% carbs / 30% fat. Low-carb and keto shift calories from carbs to fat; high-protein raises protein. The best split is the one you can stick to while hitting your protein target.'],
            ['q' => 'Do I need to count macros to lose weight?', 'a' => 'Total calories drive weight change, but hitting a protein target while in a calorie deficit helps preserve muscle and control hunger — which is why macros are worth tracking, not just calories.'],
        ],
        'related'     => ['tesamorelin', 'ipamorelin', 'mots-c', 'semaglutide'],
    ],

    // ----------------------------------------------------------------------
    'weight-loss' => [
        'slug'        => 'weight-loss',
        'name'        => 'GLP-1 Weight Loss Calculator',
        'short'       => 'Weight Loss',
        'emoji'       => '⚖️',
        'tagline'     => 'Estimate your timeline and time-to-goal on semaglutide or tirzepatide.',
        'category'    => 'Peptide',
        'accent'      => '#8B5CF6',
        'seo_title'   => 'GLP-1 Weight Loss Calculator — Semaglutide & Tirzepatide Timeline',
        'seo_description' => 'Free GLP-1 weight-loss calculator. Enter your current and goal weight to estimate how long it could take to reach it on semaglutide or tirzepatide, using average published response rates.',
        'intro'       => 'GLP-1 receptor agonists like semaglutide and tirzepatide produce gradual, steady weight loss over many months. This calculator works backwards from a goal: enter your current weight and a target weight, pick a compound, and it estimates roughly how long reaching that goal could take based on average percentage-loss curves from the clinical trials — along with your projected weight and BMI at each milestone. It is an educational estimate; individual results vary widely.',
        'how_to'      => [
            ['title' => 'Select the compound', 'body' => 'Choose semaglutide or tirzepatide — each has a different average response curve in the published trials.'],
            ['title' => 'Enter current and goal weight', 'body' => 'Provide your height, current weight and the weight you want to reach, in metric or imperial units.'],
            ['title' => 'See your estimated timeline', 'body' => 'The calculator estimates the number of weeks to reach your goal at average response rates, plus your goal BMI.'],
            ['title' => 'Review the milestones', 'body' => 'A milestone table shows projected weight and BMI at 4, 12, 24, 52 and 68 weeks so you can see the trajectory.'],
        ],
        'faqs'        => [
            ['q' => 'How much weight can you lose on semaglutide or tirzepatide?', 'a' => 'In trials, average reduction at about 68 weeks was roughly 15% of body weight for semaglutide and 20%+ for tirzepatide at higher doses. These are study averages — some people lose more, some less.'],
            ['q' => 'How long does it take to reach a weight-loss goal?', 'a' => 'Loss is gradual because the dose is titrated up over the first few months. This calculator estimates the week at which average response rates would reach your target percentage loss; very large goals may fall beyond the typical trial range.'],
            ['q' => 'Is this weight-loss estimate guaranteed?', 'a' => 'No. It is an illustrative model built on average published outcomes and does not account for your diet, activity, genetics or medical history. Use it for planning, not as a promise of results.'],
            ['q' => 'What is a realistic monthly weight loss on GLP-1s?', 'a' => 'After titration, many people lose roughly 1–2% of body weight per month on average, faster early and tapering over time. The milestone table reflects this curved, non-linear pattern.'],
        ],
        'related'     => ['semaglutide', 'tirzepatide', 'retatrutide', 'cagrilintide'],
    ],

    // ----------------------------------------------------------------------
    'glp-1-units' => [
        'slug'        => 'glp-1-units',
        'name'        => 'GLP-1 Dose Converter',
        'short'       => 'Dose Converter',
        'emoji'       => '🧮',
        'tagline'     => 'Convert a weekly GLP-1 mg dose into insulin-syringe units.',
        'category'    => 'Peptide',
        'accent'      => '#6366F1',
        'seo_title'   => 'GLP-1 Dose Converter — Semaglutide & Tirzepatide mg to Units',
        'seo_description' => 'Free GLP-1 dose converter. Reconstitute your semaglutide or tirzepatide vial and convert any weekly mg dose into the exact insulin-syringe units to draw, with a full dose-ladder reference table.',
        'intro'       => 'GLP-1 peptides are dosed weekly in milligrams, but you draw them in units on an insulin syringe — and the conversion depends entirely on how you reconstituted the vial. This converter does both steps: enter your vial size and bacteriostatic water, pick a compound, and it shows the units to draw for any weekly dose, plus a reference table for the entire standard dose ladder so you can read off every titration step at a glance.',
        'how_to'      => [
            ['title' => 'Pick a compound', 'body' => 'Choose semaglutide or tirzepatide to load its standard weekly dose ladder, or enter a custom dose.'],
            ['title' => 'Reconstitute the vial', 'body' => 'Enter the total mg of peptide in the vial and the bacteriostatic water you are adding.'],
            ['title' => 'Enter your weekly dose', 'body' => 'Type the weekly dose in mg. The converter returns the exact units and mL to draw on a U-100 syringe.'],
            ['title' => 'Use the reference table', 'body' => 'The table converts every step of the standard ladder into units for your exact reconstitution.'],
        ],
        'faqs'        => [
            ['q' => 'How do I convert semaglutide mg to units?', 'a' => 'First find your concentration: vial mg ÷ water mL = mg/mL. Then units = (weekly mg ÷ mg/mL) × 100, because a U-100 syringe has 100 units per mL. This converter runs both steps for any dose.'],
            ['q' => 'How many units is 0.25 mg of semaglutide?', 'a' => 'It depends on your concentration. For example, a 5 mg vial in 2 mL of water is 2.5 mg/mL, so 0.25 mg = 0.1 mL = 10 units. Change the vial and water inputs and the table updates for your setup.'],
            ['q' => 'What syringe should I use for GLP-1 peptides?', 'a' => 'A standard U-100 insulin syringe, marked in 100 units per mL (1 unit = 0.01 mL). The converter gives your dose directly in those units.'],
            ['q' => 'What is the standard semaglutide and tirzepatide dose ladder?', 'a' => 'Semaglutide commonly steps 0.25 → 0.5 → 1.0 → 1.7 → 2.4 mg; tirzepatide steps 2.5 → 5 → 7.5 → 10 → 12.5 → 15 mg, each held about 4 weeks. The reference table converts every step into units.'],
        ],
        'related'     => ['semaglutide', 'tirzepatide', 'retatrutide'],
    ],

];
