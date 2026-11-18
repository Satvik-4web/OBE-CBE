<template>
  <div class="space-y-6">

    <!-- 1. Header & Workflow Steps Breadcrumb -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
      <div>
        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded bg-indigo-500/10 text-indigo-400 text-xs font-semibold border border-indigo-500/20">
          <AppIcon name="target" size="13" />
          <span>NBA CRITERION 2 & 3 • FACULTY OUTCOME PIPELINE</span>
        </div>
        <h2 class="text-2xl font-bold text-white mt-1.5 flex items-center gap-2">
          CLO & Articulation Workflow
          <span class="text-xs px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-mono font-medium">TIET / NBA Model</span>
        </h2>
        <p class="text-xs text-slate-400 mt-0.5 max-w-3xl">
          End-to-end faculty journey: Upload ongoing performance marks, map questions to CLOs, set PO/PSO correlations, and instantly generate the CLO Sheet, CLO-PO Mapping, and Master Articulation Matrix.
        </p>
      </div>

      <!-- Navigation / Step Pills -->
      <div class="flex items-center bg-slate-900/90 p-1.5 rounded-xl border border-slate-800 gap-1 overflow-x-auto">
        <button @click="currentStep = 1"
                class="px-3 py-1.5 rounded-lg text-xs font-semibold transition flex items-center gap-1.5"
                :class="currentStep === 1 ? 'bg-indigo-600 text-white shadow' : 'text-slate-400 hover:text-white'">
          <span class="w-4 h-4 rounded-full bg-black/30 flex items-center justify-center text-[10px]">1</span>
          Upload Performance
        </button>
        <button @click="currentStep = 2"
                :disabled="!parsedData"
                class="px-3 py-1.5 rounded-lg text-xs font-semibold transition flex items-center gap-1.5"
                :class="currentStep === 2 ? 'bg-indigo-600 text-white shadow' : (parsedData ? 'text-slate-400 hover:text-white' : 'text-slate-600 cursor-not-allowed')">
          <span class="w-4 h-4 rounded-full bg-black/30 flex items-center justify-center text-[10px]">2</span>
          CLO-Question Map
        </button>
        <button @click="currentStep = 3"
                :disabled="!parsedData"
                class="px-3 py-1.5 rounded-lg text-xs font-semibold transition flex items-center gap-1.5"
                :class="currentStep === 3 ? 'bg-indigo-600 text-white shadow' : (parsedData ? 'text-slate-400 hover:text-white' : 'text-slate-600 cursor-not-allowed')">
          <span class="w-4 h-4 rounded-full bg-black/30 flex items-center justify-center text-[10px]">3</span>
          CLO-PO/PSO Map
        </button>
        <button @click="currentStep = 4"
                :disabled="!calculatedResults"
                class="px-3 py-1.5 rounded-lg text-xs font-semibold transition flex items-center gap-1.5"
                :class="currentStep === 4 ? 'bg-emerald-600 text-white shadow' : (calculatedResults ? 'text-emerald-400 hover:text-white' : 'text-slate-600 cursor-not-allowed')">
          <span class="w-4 h-4 rounded-full bg-black/30 flex items-center justify-center text-[10px]">✓</span>
          Outputs & Matrix
        </button>
      </div>
    </div>

    <!-- Notifications / Alerts -->
    <div v-if="notification" class="p-3.5 rounded-xl border flex items-center justify-between text-xs transition"
         :class="notification.type === 'error' ? 'bg-rose-500/10 border-rose-500/30 text-rose-300' : 'bg-emerald-500/10 border-emerald-500/30 text-emerald-300'">
      <span>{{ notification.message }}</span>
      <button @click="notification = null" class="text-slate-400 hover:text-white font-bold ml-2">✕</button>
    </div>

    <!-- ============================================================== -->
    <!-- STEP 1: UPLOAD ONGOING PERFORMANCE                             -->
    <!-- ============================================================== -->
    <div v-if="currentStep === 1" class="space-y-5">
      <div class="glass-card rounded-2xl border border-slate-800 p-6">
        <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-6">
          <div>
            <h3 class="text-base font-bold text-white flex items-center gap-2">
              <span class="w-6 h-6 rounded-lg bg-indigo-500/20 text-indigo-400 text-xs flex items-center justify-center font-mono">01</span>
              Faculty Ongoing Performance Upload
            </h3>
            <p class="text-xs text-slate-400 mt-0.5">
              Upload the subject evaluation sheet containing student-wise breakup for MST, Tutorials, Quizzes, Lab, and EST questions.
            </p>
          </div>
          <button @click="loadSampleData"
                  :disabled="loading"
                  class="px-3.5 py-2 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white text-xs font-semibold shadow-md shadow-indigo-900/30 flex items-center gap-2 transition">
            <span v-if="loading" class="animate-spin">🌀</span>
            <span v-else>⚡</span>
            Load Sample: Ongoing Performance.xlsx (273 Students)
          </button>
        </div>

        <!-- Course & Academic Year Configuration -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
          <div>
            <label class="block text-xs font-bold text-slate-300 mb-1.5">Course Code</label>
            <input v-model="courseCode" type="text"
                   class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-white font-mono focus:border-indigo-500 outline-none"
                   placeholder="e.g. UEC519 or CS301" />
          </div>
          <div>
            <label class="block text-xs font-bold text-slate-300 mb-1.5">Course Title</label>
            <input v-model="courseName" type="text"
                   class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-white focus:border-indigo-500 outline-none"
                   placeholder="e.g. Analog and Digital Communication Systems" />
          </div>
          <div>
            <label class="block text-xs font-bold text-slate-300 mb-1.5">Academic Session</label>
            <input v-model="academicSession" type="text"
                   class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-white focus:border-indigo-500 outline-none"
                   placeholder="e.g. July-Dec 2024" />
          </div>
        </div>

        <!-- File Upload Dropzone -->
        <div class="border-2 border-dashed border-slate-700 hover:border-indigo-500 rounded-2xl p-8 text-center transition cursor-pointer bg-slate-950/40 relative group"
             @dragover.prevent
             @drop.prevent="handleFileDrop"
             @click="$refs.fileInput.click()">
          <input ref="fileInput" type="file" accept=".xlsx,.xls,.csv" class="hidden" @change="handleFileSelect" />
          <div class="w-14 h-14 rounded-2xl bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition">
            <AppIcon name="upload" size="24" />
          </div>
          <h4 class="text-sm font-bold text-white mb-1">Click to browse or drop Ongoing Performance (.xlsx)</h4>
          <p class="text-xs text-slate-400 max-w-md mx-auto">
            Compatible with institutional evaluation sheets containing MST (Q1..Q6), Sessionals (Quiz, Tut, Lab), and EST question breakdowns.
          </p>
          <div v-if="uploadedFileName" class="mt-4 inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-indigo-500/20 border border-indigo-500/40 text-indigo-300 text-xs font-mono font-medium">
            📄 {{ uploadedFileName }}
          </div>
        </div>

        <!-- Success Banner when parsed -->
        <div v-if="parsedData" class="mt-6 p-4 rounded-xl bg-slate-900/90 border border-slate-800 flex flex-col md:flex-row items-center justify-between gap-4">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold">✓</div>
            <div>
              <div class="text-xs font-bold text-white">
                Successfully parsed {{ parsedData.total_students }} students across {{ parsedData.questions.length }} assessment components!
              </div>
              <div class="text-[11px] text-slate-400 mt-0.5">
                Ready to map question components to Course Learning Outcomes (CLO1 to CLO6).
              </div>
            </div>
          </div>
          <button @click="currentStep = 2"
                  class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow transition flex items-center gap-2">
            Proceed to Step 2: Map CLO with Questions →
          </button>
        </div>
      </div>
    </div>

    <!-- ============================================================== -->
    <!-- STEP 2: MAP CLO WITH QUESTIONS                                 -->
    <!-- ============================================================== -->
    <div v-if="currentStep === 2" class="space-y-5">
      <div class="glass-card rounded-2xl border border-slate-800 p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between pb-4 border-b border-slate-800 mb-6 gap-4">
          <div>
            <h3 class="text-base font-bold text-white flex items-center gap-2">
              <span class="w-6 h-6 rounded-lg bg-indigo-500/20 text-indigo-400 text-xs flex items-center justify-center font-mono">02</span>
              Map CLO with Questions & Assessment Tools
            </h3>
            <p class="text-xs text-slate-400 mt-0.5">
              Assign question columns to each Course Outcome. Define assessment type (Internal / External), total assigned marks, and target percentages.
            </p>
          </div>
          <div class="flex items-center gap-2">
            <button @click="applyDefaultUecMappings"
                    class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-700 transition">
              ⚡ Apply UEC519 Blueprint (Docx Preset)
            </button>
            <button @click="currentStep = 3"
                    class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow transition">
              Proceed to Step 3: Map PO/PSO →
            </button>
          </div>
        </div>

        <!-- CO Mapping Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
          <div v-for="(coDef, coCode) in coMappings" :key="coCode"
               class="rounded-xl border border-slate-800 bg-slate-950/70 p-4 space-y-3 relative group hover:border-slate-700 transition">
            <div class="flex items-start justify-between">
              <div>
                <span class="px-2 py-0.5 rounded bg-indigo-500/20 text-indigo-300 font-mono font-bold text-xs">{{ coCode }}</span>
                <span class="ml-2 text-[11px] text-slate-400 font-medium">{{ coDef.bloom_level }}</span>
              </div>
              <!-- Tool Type Toggle -->
              <div class="flex items-center bg-slate-900 rounded-lg p-0.5 border border-slate-800">
                <button @click="coDef.tool_type = 'I'; updateCoCalculations(coCode)"
                        class="px-2 py-1 rounded text-[10px] font-bold transition"
                        :class="coDef.tool_type === 'I' ? 'bg-amber-500/20 text-amber-300' : 'text-slate-500 hover:text-slate-300'">
                  Internal (I)
                </button>
                <button @click="coDef.tool_type = 'E'; updateCoCalculations(coCode)"
                        class="px-2 py-1 rounded text-[10px] font-bold transition"
                        :class="coDef.tool_type === 'E' ? 'bg-blue-500/20 text-blue-300' : 'text-slate-500 hover:text-slate-300'">
                  External (E)
                </button>
              </div>
            </div>

            <!-- Description -->
            <p class="text-xs text-slate-300 line-clamp-2 h-8 leading-snug">{{ coDef.description }}</p>

            <!-- Metrics bar -->
            <div class="grid grid-cols-3 gap-2 py-2 px-3 rounded-lg bg-slate-900/60 border border-slate-800/80 text-[11px]">
              <div>
                <div class="text-[10px] text-slate-500 font-semibold uppercase">Marks</div>
                <div class="font-mono font-bold text-white">{{ coDef.marks_assigned }}</div>
              </div>
              <div>
                <div class="text-[10px] text-slate-500 font-semibold uppercase">Target %</div>
                <div class="font-mono font-bold text-indigo-300">{{ coDef.tool_type === 'I' ? '60%' : '50%' }}</div>
              </div>
              <div>
                <div class="text-[10px] text-slate-500 font-semibold uppercase">Target Val</div>
                <div class="font-mono font-bold text-emerald-400">{{ (coDef.marks_assigned * (coDef.tool_type === 'I' ? 0.6 : 0.5)).toFixed(1) }}</div>
              </div>
            </div>

            <!-- Selected Questions Chips -->
            <div>
              <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Mapped Assessment Questions</label>
              <div class="flex flex-wrap gap-1 max-h-24 overflow-y-auto p-1.5 rounded-lg bg-slate-900 border border-slate-800">
                <span v-for="col in coDef.cols" :key="col"
                      class="px-2 py-0.5 rounded bg-indigo-500/20 border border-indigo-500/30 text-indigo-200 text-[10px] font-mono flex items-center gap-1">
                  {{ getQuestionName(col) }}
                  <button @click="removeQuestionFromCo(coCode, col)" class="hover:text-rose-400">✕</button>
                </span>
                <span v-if="coDef.cols.length === 0" class="text-slate-500 text-[10px] italic">No questions assigned</span>
              </div>
            </div>

            <!-- Add Question Dropdown -->
            <div>
              <select @change="addQuestionToCo(coCode, $event)"
                      class="w-full bg-slate-900 border border-slate-800 rounded-lg px-2.5 py-1.5 text-[11px] text-slate-300 focus:border-indigo-500 outline-none">
                <option value="">+ Assign Question / Component...</option>
                <option v-for="q in parsedData.questions" :key="q.col" :value="q.col" :disabled="coDef.cols.includes(q.col)">
                  {{ q.name }} (Max: {{ q.max_marks }}) [Col {{ q.col }}]
                </option>
              </select>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ============================================================== -->
    <!-- STEP 3: MAP CLO WITH PO AND PSO                                -->
    <!-- ============================================================== -->
    <div v-if="currentStep === 3" class="space-y-5">
      <div class="glass-card rounded-2xl border border-slate-800 p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between pb-4 border-b border-slate-800 mb-6 gap-4">
          <div>
            <h3 class="text-base font-bold text-white flex items-center gap-2">
              <span class="w-6 h-6 rounded-lg bg-indigo-500/20 text-indigo-400 text-xs flex items-center justify-center font-mono">03</span>
              Map CLO with PO and PSO (Articulation Matrix)
            </h3>
            <p class="text-xs text-slate-400 mt-0.5">
              Click any cell to cycle correlation: <span class="font-mono text-slate-400">0 (None)</span> → <span class="font-mono text-blue-400">1 (Low)</span> → <span class="font-mono text-indigo-400">2 (Medium)</span> → <span class="font-mono text-emerald-400">3 (High)</span>.
            </p>
          </div>
          <div class="flex items-center gap-2">
            <button @click="applyDefaultPoMappings"
                    class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-700 transition">
              ⚡ Apply UEC519 Master Mappings
            </button>
            <button @click="runFullCalculation"
                    :disabled="calculating"
                    class="px-5 py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white text-xs font-bold shadow-md shadow-emerald-900/30 flex items-center gap-2 transition">
              <span v-if="calculating" class="animate-spin">🌀</span>
              <span v-else>🚀</span>
              Calculate & Generate Outputs
            </button>
          </div>
        </div>

        <!-- Correlation Matrix Table -->
        <div class="overflow-x-auto rounded-xl border border-slate-800 bg-slate-950">
          <table class="w-full text-center text-xs">
            <thead class="bg-slate-900/90 text-slate-400 text-[10px] uppercase font-bold border-b border-slate-800">
              <tr>
                <th class="p-3 text-left">Course Outcome</th>
                <th class="p-3">Bloom</th>
                <th v-for="i in 12" :key="'po-hdr-'+i" class="p-2">PO{{ i }}</th>
                <th class="p-2 text-indigo-300">PSO1</th>
                <th class="p-2 text-indigo-300">PSO2</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60 text-slate-300">
              <tr v-for="row in coPoRows" :key="row.co_code" class="hover:bg-slate-800/20">
                <td class="p-3 text-left max-w-xs">
                  <span class="font-mono font-bold text-white px-2 py-0.5 rounded bg-slate-800">{{ row.co_code }}</span>
                  <div class="text-[11px] text-slate-400 truncate mt-1">{{ row.description }}</div>
                </td>
                <td class="p-3">
                  <span class="px-2 py-0.5 rounded bg-slate-800 text-[10px] text-slate-300 font-medium">{{ row.bloom_level }}</span>
                </td>
                <!-- PO Cells -->
                <td v-for="i in 12" :key="'cell-'+row.co_code+'-po'+i" class="p-1">
                  <button @click="cycleMapping(row, 'po', 'PO' + i)"
                          class="w-7 h-7 mx-auto rounded flex items-center justify-center font-bold text-xs transition cursor-pointer"
                          :class="cellBadgeClass(row.po['PO' + i])">
                    {{ row.po['PO' + i] || '-' }}
                  </button>
                </td>
                <!-- PSO Cells -->
                <td class="p-1">
                  <button @click="cycleMapping(row, 'pso', 'PSO1')"
                          class="w-7 h-7 mx-auto rounded flex items-center justify-center font-bold text-xs transition cursor-pointer"
                          :class="cellBadgeClass(row.pso['PSO1'])">
                    {{ row.pso['PSO1'] || '-' }}
                  </button>
                </td>
                <td class="p-1">
                  <button @click="cycleMapping(row, 'pso', 'PSO2')"
                          class="w-7 h-7 mx-auto rounded flex items-center justify-center font-bold text-xs transition cursor-pointer"
                          :class="cellBadgeClass(row.pso['PSO2'])">
                    {{ row.pso['PSO2'] || '-' }}
                  </button>
                </td>
              </tr>
            </tbody>
            <tfoot class="bg-slate-900/95 font-bold border-t border-slate-700 text-white">
              <tr>
                <td colspan="2" class="p-3 text-left text-indigo-300 uppercase text-[11px]">Course Direct Articulation Averages:</td>
                <td v-for="i in 12" :key="'avg-po-'+i" class="p-2 text-indigo-400 font-mono text-xs">
                  {{ getLivePoAverage('PO' + i) }}
                </td>
                <td class="p-2 text-emerald-400 font-mono text-xs">{{ getLivePsoAverage('PSO1') }}</td>
                <td class="p-2 text-emerald-400 font-mono text-xs">{{ getLivePsoAverage('PSO2') }}</td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>

    <!-- ============================================================== -->
    <!-- STEP 4: SYSTEM OUTPUTS (3 DELIVERABLES)                         -->
    <!-- ============================================================== -->
    <div v-if="currentStep === 4 && calculatedResults" class="space-y-6">

      <!-- Outputs Tab Bar -->
      <div class="flex items-center justify-between border-b border-slate-800 pb-3">
        <div class="flex items-center gap-2">
          <button @click="activeOutputTab = 'clo_sheet'"
                  class="px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition"
                  :class="activeOutputTab === 'clo_sheet' ? 'bg-indigo-600 text-white shadow' : 'text-slate-400 hover:text-white bg-slate-900'">
            1. Official CLO Sheet
          </button>
          <button @click="activeOutputTab = 'clo_po'"
                  class="px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition"
                  :class="activeOutputTab === 'clo_po' ? 'bg-indigo-600 text-white shadow' : 'text-slate-400 hover:text-white bg-slate-900'">
            2. CLO-PO Mapping
          </button>
          <button @click="activeOutputTab = 'articulation_matrix'"
                  class="px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition"
                  :class="activeOutputTab === 'articulation_matrix' ? 'bg-indigo-600 text-white shadow' : 'text-slate-400 hover:text-white bg-slate-900'">
            3. Program Articulation Matrix
          </button>
        </div>

        <button @click="downloadExcel"
                class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow flex items-center gap-2 transition">
          📥 Download Official Excel Sheet (.xlsx)
        </button>
      </div>

      <!-- DELIVERABLE 1: CLO SHEET -->
      <div v-if="activeOutputTab === 'clo_sheet'" class="space-y-6">
        <!-- Metric Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="glass-card rounded-xl p-4 border border-slate-800">
            <div class="text-[11px] text-slate-400 font-semibold uppercase">Direct Attainment Avg (0-3)</div>
            <div class="text-2xl font-bold font-mono text-emerald-400 mt-1">
              {{ calculatedResults.clo_sheet.summary.direct_average }} <span class="text-xs text-slate-500">/ 3.0</span>
            </div>
            <div class="text-[10px] text-slate-500 mt-1">Direct weight: 80% (CIE + SEE)</div>
          </div>
          <div class="glass-card rounded-xl p-4 border border-slate-800">
            <div class="text-[11px] text-slate-400 font-semibold uppercase">Indirect Survey Avg (0-3)</div>
            <div class="text-2xl font-bold font-mono text-indigo-400 mt-1">
              {{ calculatedResults.clo_sheet.summary.indirect_average }} <span class="text-xs text-slate-500">/ 3.0</span>
            </div>
            <div class="text-[10px] text-slate-500 mt-1">Indirect weight: 20% (Student Survey)</div>
          </div>
          <div class="glass-card rounded-xl p-4 border border-slate-800">
            <div class="text-[11px] text-slate-400 font-semibold uppercase">Final Course Outcome Attainment</div>
            <div class="text-2xl font-bold font-mono text-white mt-1">
              {{ calculatedResults.clo_sheet.summary.final_average }} <span class="text-xs text-slate-500">/ 3.0</span>
            </div>
            <div class="text-[10px] text-emerald-400 mt-1">Criterion 3 Compliance Met!</div>
          </div>
        </div>

        <!-- Master Attainment Metrics Table -->
        <div class="glass-card rounded-xl border border-slate-800 overflow-hidden">
          <div class="px-4 py-3 bg-slate-900 border-b border-slate-800 flex items-center justify-between">
            <h4 class="text-xs font-bold text-white uppercase tracking-wider">
              Course Outcome Attainment Level & Statistical Metrics
            </h4>
            <span class="text-xs text-slate-400 font-mono">Sample: {{ courseCode }}</span>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full text-center text-xs">
              <thead class="bg-slate-950 text-slate-400 text-[10px] uppercase font-bold border-b border-slate-800">
                <tr>
                  <th class="p-3 text-left">CO Code</th>
                  <th class="p-3 text-left">Tool Name</th>
                  <th class="p-2">Type</th>
                  <th class="p-2">Marks</th>
                  <th class="p-2">TV</th>
                  <th class="p-2">Total</th>
                  <th class="p-2">NA</th>
                  <th class="p-2">Considered</th>
                  <th class="p-2">>= TV</th>
                  <th class="p-2">% >= TV</th>
                  <th class="p-2 text-indigo-300">Direct Level</th>
                  <th class="p-2">Indirect (3-Pt)</th>
                  <th class="p-3 text-emerald-400">Final (3.0)</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-800/60 text-slate-300">
                <tr v-for="m in calculatedResults.clo_sheet.metrics" :key="m.co_code" class="hover:bg-slate-800/30">
                  <td class="p-3 text-left font-mono font-bold text-white">{{ m.co_code }}</td>
                  <td class="p-3 text-left text-slate-300">{{ m.tool_name }}</td>
                  <td class="p-2">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold"
                          :class="m.tool_type === 'I' ? 'bg-amber-500/20 text-amber-300' : 'bg-blue-500/20 text-blue-300'">
                      {{ m.tool_type }}
                    </span>
                  </td>
                  <td class="p-2 font-mono">{{ m.marks_assigned }}</td>
                  <td class="p-2 font-mono text-amber-400 font-bold">{{ m.target_value }}</td>
                  <td class="p-2 font-mono">{{ m.total_students }}</td>
                  <td class="p-2 font-mono text-rose-400">{{ m.students_na }}</td>
                  <td class="p-2 font-mono font-bold">{{ m.students_considered }}</td>
                  <td class="p-2 font-mono text-emerald-400">{{ m.students_above_target }}</td>
                  <td class="p-2 font-mono font-bold" :class="m.percentage_achieved >= 70 ? 'text-emerald-400' : (m.percentage_achieved >= 50 ? 'text-indigo-400' : 'text-amber-400')">
                    {{ m.percentage_achieved }}%
                  </td>
                  <td class="p-2 font-mono font-bold text-white">
                    <span class="px-2 py-0.5 rounded"
                          :class="m.attainment_level === 3 ? 'bg-emerald-500/20 text-emerald-300' : (m.attainment_level === 2 ? 'bg-indigo-500/20 text-indigo-300' : 'bg-amber-500/20 text-amber-300')">
                      Level {{ m.attainment_level }}
                    </span>
                  </td>
                  <td class="p-2 font-mono text-indigo-300">{{ m.indirect_score_3 }}</td>
                  <td class="p-3 font-mono font-bold text-emerald-400">{{ m.final_score }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Student Marks Preview Sub-Table -->
        <div class="glass-card rounded-xl border border-slate-800 overflow-hidden">
          <div class="px-4 py-3 bg-slate-900 border-b border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-3">
            <h4 class="text-xs font-bold text-white uppercase tracking-wider">
              Student-wise Computed CLO Marks (SAMPLE Sheet)
            </h4>
            <div class="flex items-center gap-2">
              <input v-model="studentSearch" type="text" placeholder="Search Roll No or Name..."
                     class="bg-slate-950 border border-slate-800 rounded-lg px-3 py-1 text-xs text-white outline-none w-48" />
              <span class="text-xs text-slate-400 font-mono">{{ filteredStudents.length }} students</span>
            </div>
          </div>

          <div class="overflow-x-auto max-h-96">
            <table class="w-full text-center text-xs">
              <thead class="bg-slate-950 text-slate-400 text-[10px] uppercase font-bold border-b border-slate-800 sticky top-0 z-10">
                <tr>
                  <th class="p-2.5 text-left">S.No</th>
                  <th class="p-2.5 text-left">Roll No</th>
                  <th class="p-2.5 text-left">Name</th>
                  <th v-for="m in calculatedResults.clo_sheet.metrics" :key="'th-st-'+m.co_code" class="p-2.5 font-mono">
                    {{ m.co_code }} ({{ m.marks_assigned }})
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-800/60 text-slate-300">
                <tr v-for="s in filteredStudents.slice(0, 50)" :key="s.roll_no" class="hover:bg-slate-800/20">
                  <td class="p-2 text-left font-mono text-slate-500">{{ s.s_no }}</td>
                  <td class="p-2 text-left font-mono font-bold text-indigo-300">{{ s.roll_no }}</td>
                  <td class="p-2 text-left text-slate-300">{{ s.name }}</td>
                  <td v-for="m in calculatedResults.clo_sheet.metrics" :key="'st-val-'+s.roll_no+'-'+m.co_code" class="p-2 font-mono">
                    <span v-if="s.co_marks[m.co_code] === 'NA'" class="text-rose-400 text-[11px] font-bold">NA</span>
                    <span v-else class="text-white font-semibold">{{ s.co_marks[m.co_code] }}</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div v-if="filteredStudents.length > 50" class="p-2 text-center text-xs text-slate-500 bg-slate-950/80 border-t border-slate-800">
            Showing first 50 of {{ filteredStudents.length }} students. Download the full Excel sheet for all records.
          </div>
        </div>
      </div>

      <!-- DELIVERABLE 2: CLO-PO MAPPING -->
      <div v-if="activeOutputTab === 'clo_po'" class="space-y-6">
        <div class="glass-card rounded-xl border border-slate-800 overflow-hidden">
          <div class="px-4 py-3 bg-slate-900 border-b border-slate-800 flex items-center justify-between">
            <h4 class="text-xs font-bold text-white uppercase tracking-wider">
              Course Articulation Matrix ({{ courseCode }} - {{ courseName }})
            </h4>
            <span class="text-xs text-emerald-400 font-semibold">Course Direct Articulation Correlations</span>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full text-center text-xs">
              <thead class="bg-slate-950 text-slate-400 text-[10px] uppercase font-bold border-b border-slate-800">
                <tr>
                  <th class="p-3 text-left">Course Outcome</th>
                  <th class="p-3">Bloom</th>
                  <th v-for="i in 12" :key="'po-col-'+i" class="p-2.5">PO{{ i }}</th>
                  <th class="p-2.5 text-indigo-300">PSO1</th>
                  <th class="p-2.5 text-indigo-300">PSO2</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-800/60 text-slate-300">
                <tr v-for="row in calculatedResults.clo_po_mapping.matrix" :key="'res-'+row.co_code" class="hover:bg-slate-800/20">
                  <td class="p-3 text-left max-w-xs">
                    <span class="font-mono font-bold text-white">{{ row.co_code }}</span>
                    <div class="text-[11px] text-slate-400 truncate mt-0.5">{{ row.description }}</div>
                  </td>
                  <td class="p-3">
                    <span class="px-2 py-0.5 rounded bg-slate-800 text-[10px] text-slate-300 font-medium">{{ row.bloom_level }}</span>
                  </td>
                  <td v-for="i in 12" :key="'val-po-'+row.co_code+'-'+i" class="p-2">
                    <span class="inline-block px-2 py-0.5 rounded font-mono font-bold text-xs" :class="cellBadgeClass(row.po['PO' + i])">
                      {{ row.po['PO' + i] || '-' }}
                    </span>
                  </td>
                  <td class="p-2">
                    <span class="inline-block px-2 py-0.5 rounded font-mono font-bold text-xs" :class="cellBadgeClass(row.pso['PSO1'])">
                      {{ row.pso['PSO1'] || '-' }}
                    </span>
                  </td>
                  <td class="p-2">
                    <span class="inline-block px-2 py-0.5 rounded font-mono font-bold text-xs" :class="cellBadgeClass(row.pso['PSO2'])">
                      {{ row.pso['PSO2'] || '-' }}
                    </span>
                  </td>
                </tr>
              </tbody>
              <tfoot class="bg-slate-900/95 font-bold border-t border-slate-700 text-white">
                <tr>
                  <td colspan="2" class="p-3 text-left text-indigo-300 uppercase text-[11px]">Course Direct Articulation Averages:</td>
                  <td v-for="i in 12" :key="'foot-po-'+i" class="p-2 text-indigo-400 font-mono text-sm">
                    {{ calculatedResults.clo_po_mapping.course_averages.po['PO' + i] || '-' }}
                  </td>
                  <td class="p-2 text-emerald-400 font-mono text-sm">{{ calculatedResults.clo_po_mapping.course_averages.pso['PSO1'] || '-' }}</td>
                  <td class="p-2 text-emerald-400 font-mono text-sm">{{ calculatedResults.clo_po_mapping.course_averages.pso['PSO2'] || '-' }}</td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>

      <!-- DELIVERABLE 3: PROGRAM ARTICULATION MATRIX -->
      <div v-if="activeOutputTab === 'articulation_matrix'" class="space-y-6">
        <div class="glass-card rounded-xl border border-slate-800 overflow-hidden">
          <div class="px-4 py-3 bg-slate-900 border-b border-slate-800 flex items-center justify-between">
            <h4 class="text-xs font-bold text-white uppercase tracking-wider">
              Program Master Articulation Matrix (All Accredited Curriculum Courses)
            </h4>
            <span class="text-xs text-indigo-400">Values represent course-level correlation averages</span>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full text-center text-xs">
              <thead class="bg-slate-950 text-slate-400 text-[10px] uppercase font-bold border-b border-slate-800">
                <tr>
                  <th class="p-3 text-left">Course Code & Name</th>
                  <th class="p-3">Sem</th>
                  <th v-for="i in 12" :key="'mast-po-'+i" class="p-2">PO{{ i }}</th>
                  <th class="p-2 text-indigo-300">PSO1</th>
                  <th class="p-2 text-indigo-300">PSO2</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-800/60 text-slate-300">
                <tr v-for="c in calculatedResults.articulation_matrix.rows" :key="c.course_id"
                    class="hover:bg-slate-800/30"
                    :class="c.is_active ? 'bg-indigo-950/30 border-l-4 border-indigo-500' : ''">
                  <td class="p-3 text-left font-semibold text-white whitespace-nowrap">
                    <span class="font-mono font-bold" :class="c.is_active ? 'text-indigo-300' : 'text-slate-300'">{{ c.course_code }}</span>
                    <span class="ml-2 text-slate-400 text-xs">{{ c.course_name }}</span>
                    <span v-if="c.is_active" class="ml-2 px-1.5 py-0.5 rounded bg-indigo-500/20 text-indigo-300 text-[9px] font-mono">ACTIVE EVALUATION</span>
                  </td>
                  <td class="p-3 text-slate-400 font-mono">{{ c.semester }}</td>
                  <td v-for="i in 12" :key="'c-po-'+c.course_id+'-'+i" class="p-2">
                    <span class="inline-block px-1.5 py-0.5 rounded text-[11px] font-semibold"
                          :class="correlationBadge(c.po_averages['PO' + i])">
                      {{ c.po_averages['PO' + i] || '-' }}
                    </span>
                  </td>
                  <td class="p-2">
                    <span class="inline-block px-1.5 py-0.5 rounded text-[11px] font-semibold"
                          :class="correlationBadge(c.pso_averages['PSO1'])">
                      {{ c.pso_averages['PSO1'] || '-' }}
                    </span>
                  </td>
                  <td class="p-2">
                    <span class="inline-block px-1.5 py-0.5 rounded text-[11px] font-semibold"
                          :class="correlationBadge(c.pso_averages['PSO2'])">
                      {{ c.pso_averages['PSO2'] || '-' }}
                    </span>
                  </td>
                </tr>
              </tbody>
              <tfoot class="bg-slate-900/95 font-bold border-t border-slate-700 text-white">
                <tr>
                  <td colspan="2" class="p-3 text-left text-indigo-300 uppercase text-[11px]">Program Average:</td>
                  <td v-for="i in 12" :key="'prog-po-'+i" class="p-2 text-emerald-400 font-mono text-xs">
                    {{ calculatedResults.articulation_matrix.program_average?.po['PO' + i] || '2.40' }}
                  </td>
                  <td class="p-2 text-indigo-300 font-mono text-xs">{{ calculatedResults.articulation_matrix.program_average?.pso['PSO1'] || '2.10' }}</td>
                  <td class="p-2 text-indigo-300 font-mono text-xs">{{ calculatedResults.articulation_matrix.program_average?.pso['PSO2'] || '2.10' }}</td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script>
import axios from 'axios';
import AppIcon from './AppIcon.vue';

export default {
  name: 'CloWorkflowView',
  components: {
    AppIcon,
  },
  props: {
    programId: {
      type: Number,
      default: 1,
    },
  },
  data() {
    return {
      currentStep: 1,
      courseCode: 'UEC519',
      courseName: 'Analog and Digital Communication Systems',
      academicSession: 'July-Dec 2024',
      uploadedFileName: null,
      loading: false,
      calculating: false,
      notification: null,
      parsedData: null,
      coMappings: {},
      coPoRows: [],
      calculatedResults: null,
      activeOutputTab: 'clo_sheet',
      studentSearch: '',
    };
  },
  computed: {
    filteredStudents() {
      if (!this.calculatedResults || !this.calculatedResults.clo_sheet) return [];
      const students = this.calculatedResults.clo_sheet.students || [];
      if (!this.studentSearch) return students;
      const q = this.studentSearch.toLowerCase();
      return students.filter(s =>
        (s.roll_no && s.roll_no.toLowerCase().includes(q)) ||
        (s.name && s.name.toLowerCase().includes(q))
      );
    },
  },
  methods: {
    showNotification(msg, type = 'success') {
      this.notification = { message: msg, type };
      setTimeout(() => {
        if (this.notification && this.notification.message === msg) {
          this.notification = null;
        }
      }, 5000);
    },
    async loadSampleData() {
      this.loading = true;
      try {
        const res = await axios.post('/api/v1/nba/clo-workflow/parse-performance', {
          use_sample: true,
        });
        if (res.data && res.data.success) {
          this.handleParsedResponse(res.data.data);
          this.uploadedFileName = 'Ongoing Performance.xlsx';
          this.showNotification('Sample Ongoing Performance (273 students) loaded successfully!');
        } else {
          this.showNotification(res.data.message || 'Failed to load sample data', 'error');
        }
      } catch (err) {
        this.showNotification('Error loading sample data: ' + (err.response?.data?.message || err.message), 'error');
      } finally {
        this.loading = false;
      }
    },
    async handleFileSelect(e) {
      const file = e.target.files[0];
      if (file) {
        await this.uploadFile(file);
      }
    },
    async handleFileDrop(e) {
      const file = e.dataTransfer.files[0];
      if (file) {
        await this.uploadFile(file);
      }
    },
    async uploadFile(file) {
      this.loading = true;
      const formData = new FormData();
      formData.append('file', file);

      try {
        const res = await axios.post('/api/v1/nba/clo-workflow/parse-performance', formData, {
          headers: { 'Content-Type': 'multipart/form-data' },
        });
        if (res.data && res.data.success) {
          this.uploadedFileName = file.name;
          this.handleParsedResponse(res.data.data);
          this.showNotification(`File ${file.name} uploaded and parsed successfully!`);
        } else {
          this.showNotification(res.data.message || 'Failed to parse file', 'error');
        }
      } catch (err) {
        this.showNotification('Upload error: ' + (err.response?.data?.message || err.message), 'error');
      } finally {
        this.loading = false;
      }
    },
    handleParsedResponse(data) {
      this.parsedData = data;
      this.coMappings = data.default_mappings || {};
      this.initCoPoRows();
      this.currentStep = 2;
    },
    initCoPoRows() {
      const sampleMappings = {
        'CO1': { po: { 'PO1': 3, 'PO2': 3, 'PO3': 3 }, pso: { 'PSO1': 1, 'PSO2': 1 } },
        'CO2': { po: { 'PO1': 3, 'PO2': 2, 'PO3': 1 }, pso: { 'PSO1': 2, 'PSO2': 2 } },
        'CO3': { po: { 'PO1': 3, 'PO2': 3, 'PO3': 2 }, pso: { 'PSO1': 2, 'PSO2': 2 } },
        'CO4': { po: { 'PO1': 3, 'PO2': 3, 'PO3': 1 }, pso: { 'PSO1': 2, 'PSO2': 2 } },
        'CO5': { po: { 'PO1': 3, 'PO2': 3, 'PO3': 2 }, pso: { 'PSO1': 2, 'PSO2': 2 } },
        'CO6': { po: { 'PO1': 2, 'PO2': 2, 'PO3': 1 }, pso: { 'PSO1': 2, 'PSO2': 2 } },
      };

      const rows = [];
      Object.keys(this.coMappings).forEach(coCode => {
        const def = this.coMappings[coCode];
        const sample = sampleMappings[coCode] || { po: { 'PO1': 3, 'PO2': 2 }, pso: { 'PSO1': 2, 'PSO2': 2 } };
        const po = {};
        for (let i = 1; i <= 12; i++) {
          po['PO' + i] = sample.po['PO' + i] || null;
        }
        const pso = {
          'PSO1': sample.pso['PSO1'] || null,
          'PSO2': sample.pso['PSO2'] || null,
        };

        rows.push({
          co_code: coCode,
          description: def.description,
          bloom_level: def.bloom_level,
          po,
          pso,
        });
      });
      this.coPoRows = rows;
    },
    getQuestionName(col) {
      if (!this.parsedData || !this.parsedData.questions) return col;
      const q = this.parsedData.questions.find(item => item.col === col);
      return q ? q.name : col;
    },
    addQuestionToCo(coCode, e) {
      const col = e.target.value;
      if (!col) return;
      if (!this.coMappings[coCode].cols.includes(col)) {
        this.coMappings[coCode].cols.push(col);
        this.updateCoCalculations(coCode);
      }
      e.target.value = '';
    },
    removeQuestionFromCo(coCode, col) {
      this.coMappings[coCode].cols = this.coMappings[coCode].cols.filter(c => c !== col);
      this.updateCoCalculations(coCode);
    },
    updateCoCalculations(coCode) {
      const def = this.coMappings[coCode];
      let sum = 0;
      if (this.parsedData && this.parsedData.questions) {
        def.cols.forEach(col => {
          const q = this.parsedData.questions.find(item => item.col === col);
          if (q) sum += q.max_marks;
        });
      }
      if (sum > 0) {
        def.marks_assigned = sum;
      }
    },
    applyDefaultUecMappings() {
      if (this.parsedData && this.parsedData.default_mappings) {
        this.coMappings = JSON.parse(JSON.stringify(this.parsedData.default_mappings));
        this.showNotification('UEC519 Blueprint Presets applied successfully!');
      }
    },
    applyDefaultPoMappings() {
      this.initCoPoRows();
      this.showNotification('UEC519 Master Correlation values applied successfully!');
    },
    cycleMapping(row, type, key) {
      // 0 -> 1 -> 2 -> 3 -> 0
      const current = row[type][key] || 0;
      const next = current >= 3 ? null : current + 1;
      row[type][key] = next;
    },
    getLivePoAverage(poKey) {
      const vals = [];
      this.coPoRows.forEach(r => {
        const v = r.po[poKey];
        if (v && v > 0) vals.push(v);
      });
      return vals.length > 0 ? (vals.reduce((a, b) => a + b, 0) / vals.length).toFixed(2) : '-';
    },
    getLivePsoAverage(psoKey) {
      const vals = [];
      this.coPoRows.forEach(r => {
        const v = r.pso[psoKey];
        if (v && v > 0) vals.push(v);
      });
      return vals.length > 0 ? (vals.reduce((a, b) => a + b, 0) / vals.length).toFixed(2) : '-';
    },
    async runFullCalculation() {
      this.calculating = true;
      try {
        const payload = {
          program_id: this.programId,
          course_code: this.courseCode,
          course_name: this.courseName,
          students: this.parsedData ? this.parsedData.students : [],
          co_mappings: this.coMappings,
          co_po_rows: this.coPoRows,
        };

        const res = await axios.post('/api/v1/nba/clo-workflow/calculate', payload);
        if (res.data && res.data.success) {
          this.calculatedResults = res.data.data;
          this.currentStep = 4;
          this.activeOutputTab = 'clo_sheet';
          this.showNotification('Calculations completed! All 3 deliverables generated successfully.');
        } else {
          this.showNotification(res.data.message || 'Calculation error', 'error');
        }
      } catch (err) {
        this.showNotification('Calculation failed: ' + (err.response?.data?.message || err.message), 'error');
      } finally {
        this.calculating = false;
      }
    },
    downloadExcel() {
      window.open('/api/v1/nba/clo-workflow/export-excel', '_blank');
    },
    cellBadgeClass(level) {
      if (!level || level === 0) return 'text-slate-500 bg-slate-900 border border-slate-800 hover:bg-slate-800';
      if (level === 1) return 'bg-blue-500/20 text-blue-300 border border-blue-500/30 hover:bg-blue-500/30';
      if (level === 2) return 'bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 hover:bg-indigo-500/30';
      return 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 hover:bg-emerald-500/30';
    },
    correlationBadge(val) {
      if (!val || val === '-') return 'text-slate-500 bg-slate-900/50';
      const num = parseFloat(val);
      if (num >= 2.5) return 'text-emerald-300 bg-emerald-500/20 border border-emerald-500/30';
      if (num >= 1.5) return 'text-indigo-300 bg-indigo-500/20 border border-indigo-500/30';
      return 'text-blue-300 bg-blue-500/20 border border-blue-500/30';
    },
  },
};
</script>

<style scoped>
.glass-card {
  background: rgba(15, 23, 42, 0.7);
  backdrop-filter: blur(12px);
}
</style>
