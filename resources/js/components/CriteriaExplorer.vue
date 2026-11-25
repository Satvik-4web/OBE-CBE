<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded bg-indigo-500/10 text-indigo-400 text-xs font-semibold border border-indigo-500/20">
          NBA Tier-II SAR Criteria (1 to 10)
        </div>
        <h3 class="text-xl font-bold text-white mt-1">
          Accreditation Criteria & Claimed Scores
        </h3>
      </div>
      <div class="text-right">
        <span class="text-xs text-slate-400">Total Score:</span>
        <span class="ml-1 text-xl font-black text-emerald-400">{{ sar.total_claimed_marks }}</span>
        <span class="text-xs text-slate-400"> / 1000.0</span>
      </div>
    </div>

    <!-- Criteria Accordion / List -->
    <div class="space-y-3">
      <div v-for="c in criteria" :key="c.number"
           class="glass-card rounded-xl overflow-hidden border border-slate-800 transition">
        <div @click="toggleExpand(c.number)"
             class="p-4 bg-slate-900/60 flex items-center justify-between cursor-pointer hover:bg-slate-900/90 transition">
          <div class="flex items-center space-x-3">
            <span class="w-8 h-8 rounded-lg bg-indigo-500/10 border border-indigo-500/30 flex items-center justify-center font-bold text-indigo-300 text-sm">
              C{{ c.number }}
            </span>
            <div>
              <div class="text-sm font-bold text-white">{{ c.title }}</div>
              <div class="text-xs text-slate-400">Criterion {{ c.number }} • Max Marks: {{ c.max_marks }}</div>
            </div>
          </div>

          <div class="flex items-center space-x-4">
            <div class="text-right">
              <div class="text-base font-bold text-emerald-400">
                {{ c.claimed_marks }} <span class="text-xs text-slate-400 font-normal">/ {{ c.max_marks }}</span>
              </div>
              <div class="text-[10px] text-slate-400">{{ c.percentage }}% Claimed</div>
            </div>
            <span class="text-slate-400 text-sm">{{ expanded === c.number ? '▲' : '▼' }}</span>
          </div>
        </div>

        <!-- Expanded Details -->
        <div v-if="expanded === c.number" class="p-5 border-t border-slate-800 bg-slate-950/40 space-y-4">
          <div>
            <h5 class="text-xs font-bold text-slate-300 uppercase tracking-wider">Self-Assessment Summary</h5>
            <p class="text-xs text-slate-300 mt-1 leading-relaxed">{{ c.summary }}</p>
          </div>

          <!-- Specific Criterion In-Depth Data -->
          <div v-if="c.number === 4" class="p-3 rounded-lg bg-slate-900 border border-slate-800 text-xs space-y-2">
            <div class="font-semibold text-indigo-300">Criterion 4 Metrics Snapshot:</div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
              <div class="p-2 rounded bg-slate-950">
                <div class="text-slate-400 text-[10px]">Success Rate SI (w/o Backlog)</div>
                <div class="font-bold text-white font-mono">{{ sar.live_engines?.success_rate?.mean_si_without_backlog || 0.85 }} ({{ sar.live_engines?.success_rate?.marks_without_backlog || 21.25 }}/25)</div>
              </div>
              <div class="p-2 rounded bg-slate-950">
                <div class="text-slate-400 text-[10px]">Graduation in Stipulated Time</div>
                <div class="font-bold text-white font-mono">{{ sar.live_engines?.success_rate?.mean_si_stipulated_time || 0.98 }} ({{ sar.live_engines?.success_rate?.marks_stipulated_time || 14.7 }}/15)</div>
              </div>
              <div class="p-2 rounded bg-slate-950">
                <div class="text-slate-400 text-[10px]">Placement & Higher Studies Index</div>
                <div class="font-bold text-emerald-400 font-mono">{{ (sar.live_engines?.placement?.avg_placement_index * 100).toFixed(1) || 84.5 }}% ({{ sar.live_engines?.placement?.claimed_marks || 36 }}/40)</div>
              </div>
            </div>
          </div>

          <!-- Criterion 5 In-Depth Data -->
          <div v-if="c.number === 5" class="p-4 rounded-xl bg-slate-900 border border-slate-800 text-xs space-y-3">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
              <div>
                <span class="font-bold text-white text-sm">Criterion 5: Faculty Information & Contributions</span>
                <p class="text-[11px] text-slate-400 mt-0.5">Tier-II Evaluated Cycle (CAY, CAYm1, CAYm2) • 5 Sub-criteria Assessment</p>
              </div>
              <button
                @click="$emit('navigate', 'criterion5')"
                class="px-3 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs shadow-md transition flex items-center gap-1.5 shrink-0"
              >
                <span>🚀</span> Open Full Criterion 5 Module (5.1 - 5.5)
              </button>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-2.5 pt-1">
              <div class="p-2.5 rounded-lg bg-slate-950 border border-slate-800/80">
                <div class="text-slate-400 text-[10px] uppercase font-semibold">5.1 SFR (Avg: 19.04)</div>
                <div class="font-bold text-emerald-400 font-mono text-sm mt-0.5">{{ sar.live_engines?.criterion5?.sub_criteria?.['5.1']?.claimed_marks || 18.0 }} <span class="text-slate-500 text-[10px] font-normal">/ 30</span></div>
              </div>
              <div class="p-2.5 rounded-lg bg-slate-950 border border-slate-800/80">
                <div class="text-slate-400 text-[10px] uppercase font-semibold">5.2 FQI (Avg: 22.12)</div>
                <div class="font-bold text-emerald-400 font-mono text-sm mt-0.5">{{ sar.live_engines?.criterion5?.sub_criteria?.['5.2']?.claimed_marks || 22.12 }} <span class="text-slate-500 text-[10px] font-normal">/ 25</span></div>
              </div>
              <div class="p-2.5 rounded-lg bg-slate-950 border border-slate-800/80">
                <div class="text-slate-400 text-[10px] uppercase font-semibold">5.3 Cadre (1:2:6)</div>
                <div class="font-bold text-emerald-400 font-mono text-sm mt-0.5">{{ sar.live_engines?.criterion5?.sub_criteria?.['5.3']?.claimed_marks || 20.69 }} <span class="text-slate-500 text-[10px] font-normal">/ 25</span></div>
              </div>
              <div class="p-2.5 rounded-lg bg-slate-950 border border-slate-800/80">
                <div class="text-slate-400 text-[10px] uppercase font-semibold">5.4 Visiting Faculty</div>
                <div class="font-bold text-emerald-400 font-mono text-sm mt-0.5">{{ sar.live_engines?.criterion5?.sub_criteria?.['5.4']?.claimed_marks || 10.0 }} <span class="text-slate-500 text-[10px] font-normal">/ 10</span></div>
              </div>
              <div class="p-2.5 rounded-lg bg-slate-950 border border-slate-800/80">
                <div class="text-slate-400 text-[10px] uppercase font-semibold">5.5 Retention</div>
                <div class="font-bold text-emerald-400 font-mono text-sm mt-0.5">{{ sar.live_engines?.criterion5?.sub_criteria?.['5.5']?.claimed_marks || 7.83 }} <span class="text-slate-500 text-[10px] font-normal">/ 10</span></div>
              </div>
            </div>
          </div>

          <div v-if="c.number === 10" class="p-3 rounded-lg bg-slate-900 border border-slate-800 text-xs space-y-2">
            <div class="font-semibold text-indigo-300">Criterion 10 Budget & Governance Snapshot:</div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
              <div class="p-2 rounded bg-slate-950">
                <div class="text-slate-400 text-[10px]">Total 3-Year Expenditure</div>
                <div class="font-bold text-white">₹{{ (sar.live_engines?.budget?.total_spent || 15370000).toLocaleString() }}</div>
              </div>
              <div class="p-2 rounded bg-slate-950">
                <div class="text-slate-400 text-[10px]">Budget Utilization Percentage</div>
                <div class="font-bold text-emerald-400 font-mono">{{ sar.live_engines?.budget?.overall_utilization || 94.3 }}% ({{ sar.live_engines?.budget?.claimed_marks || 28 }}/30 Pts)</div>
              </div>
            </div>
          </div>

          <!-- Evidence Documents & Version Control Section -->
          <div class="mt-6 border-t border-slate-800 pt-4">
            <div class="flex items-center justify-between mb-3">
              <h5 class="text-xs font-bold text-slate-300 uppercase tracking-wider flex items-center gap-2">
                <span>📎</span> Supporting Evidence (PDF Version Control)
              </h5>
              
              <div class="flex items-center gap-2">
                <button
                  @click="openVersionHistory(c.number)"
                  class="px-2.5 py-1.5 rounded bg-slate-800 hover:bg-slate-700 text-indigo-300 text-xs font-medium border border-slate-700 transition flex items-center gap-1"
                >
                  <span>📜</span> Version History
                </button>

                <div v-if="!sar.is_locked">
                  <label :for="'upload-' + c.number" class="cursor-pointer px-3 py-1.5 rounded bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-medium transition flex items-center gap-1">
                    <span v-if="uploading[c.number]">Uploading...</span>
                    <span v-else>+ Upload New Version</span>
                  </label>
                  <input type="file" :id="'upload-' + c.number" accept=".pdf" class="hidden" :disabled="uploading[c.number]" @change="uploadEvidence($event, c.number)" />
                </div>
                <div v-else>
                  <span class="px-3 py-1 rounded bg-slate-800 text-slate-400 text-xs font-medium border border-slate-700">🔒 SAR Locked</span>
                </div>
              </div>
            </div>

            <!-- Upload Reason Field -->
            <div v-if="!sar.is_locked" class="mb-3">
              <input
                type="text"
                v-model="uploadReason[c.number]"
                placeholder="Optional: Enter revision notes / reason for uploading this version..."
                class="w-full bg-slate-900 border border-slate-800 rounded-lg px-3 py-1.5 text-xs text-slate-200 placeholder-slate-500 focus:border-indigo-500 outline-none"
              />
            </div>

            <div v-if="!evidences[c.number] || evidences[c.number].length === 0" class="p-4 rounded-lg bg-slate-900/50 border border-slate-800/50 text-center">
              <span class="text-xs text-slate-500">No evidence documents uploaded yet for Criterion {{ c.number }}.</span>
            </div>
            
            <div v-else class="space-y-2">
              <div v-for="ev in evidences[c.number]" :key="ev.id" class="flex items-center justify-between p-3 rounded-lg bg-slate-900 border border-slate-800">
                <div class="flex items-center gap-3">
                  <span class="w-8 h-8 rounded bg-rose-500/10 text-rose-400 flex items-center justify-center font-bold text-xs">
                    📄
                  </span>
                  <div>
                    <div class="flex items-center gap-2">
                      <span class="text-xs font-medium text-slate-200">{{ ev.file_name }}</span>
                      <span class="px-1.5 py-0.5 rounded bg-indigo-500/20 text-indigo-300 text-[10px] font-mono font-bold border border-indigo-500/30">
                        v{{ ev.version || 1 }}
                      </span>
                    </div>
                    <div class="text-[10px] text-slate-500 mt-0.5">
                      Uploaded by {{ ev.uploaded_by }} • {{ new Date(ev.created_at).toLocaleString() }}
                      <span v-if="ev.upload_reason" class="ml-1 text-slate-400 italic">• Reason: "{{ ev.upload_reason }}"</span>
                    </div>
                  </div>
                </div>
                
                <div class="flex items-center gap-2">
                  <button @click="downloadEvidence(ev)" class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-slate-300 text-[10px] transition font-medium border border-slate-700">
                    Download
                  </button>
                  <button v-if="!sar.is_locked" @click="deleteEvidence(ev.id, c.number)" class="px-2.5 py-1 rounded bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 text-[10px] transition font-medium border border-rose-500/20">
                    Delete
                  </button>
                </div>
              </div>
            </div>
          </div>
          
        </div>
      </div>
    </div>

    <!-- Version History Modal -->
    <div v-if="showHistoryModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4">
      <div class="w-full max-w-2xl rounded-xl border border-slate-700 bg-slate-900 p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
          <div>
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
              <span>📜</span> Criterion {{ historyCriterionNumber }} Evidence Version History
            </h3>
            <p class="text-xs text-slate-400 mt-0.5">
              Chronological audit history of all document revisions uploaded for Criterion {{ historyCriterionNumber }}.
            </p>
          </div>
          <button @click="showHistoryModal = false" class="text-slate-400 hover:text-white text-xl">✕</button>
        </div>

        <div v-if="loadingHistory" class="py-8 text-center text-xs text-slate-400 space-y-2">
          <div class="w-6 h-6 border-2 border-indigo-500/20 border-t-indigo-500 rounded-full animate-spin mx-auto"></div>
          <div>Loading version timeline...</div>
        </div>

        <div v-else-if="historyList.length === 0" class="py-8 text-center text-xs text-slate-500">
          No version history recorded for this criterion.
        </div>

        <div v-else class="max-h-96 overflow-y-auto space-y-3 pr-1">
          <div v-for="item in historyList" :key="item.id" class="p-3 rounded-lg bg-slate-950 border border-slate-800 flex items-center justify-between">
            <div class="flex items-start gap-3">
              <span class="px-2 py-1 rounded bg-indigo-600 text-white font-mono font-bold text-xs shrink-0">
                v{{ item.version || 1 }}
              </span>
              <div>
                <div class="text-xs font-semibold text-white">{{ item.file_name }}</div>
                <div class="text-[10px] text-slate-400 mt-0.5">
                  Uploaded by <strong class="text-slate-200">{{ item.uploaded_by }}</strong> on {{ new Date(item.created_at).toLocaleString() }}
                </div>
                <div v-if="item.upload_reason" class="text-[11px] text-indigo-300 mt-1 bg-indigo-500/10 px-2 py-0.5 rounded border border-indigo-500/20 inline-block">
                  Note: {{ item.upload_reason }}
                </div>
              </div>
            </div>

            <button
              @click="downloadEvidence(item)"
              class="px-3 py-1.5 rounded bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-medium transition border border-slate-700 shrink-0"
            >
              Download v{{ item.version }}
            </button>
          </div>
        </div>

        <div class="flex justify-end pt-2 border-t border-slate-800">
          <button @click="showHistoryModal = false" class="px-4 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold transition">
            Close History
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'CriteriaExplorer',
  emits: ['navigate'],
  props: {
    criteria: {
      type: Array,
      default: () => [],
    },
    sar: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      expanded: 1,
      evidences: {},
      uploading: {},
      uploadReason: {},
      showHistoryModal: false,
      historyList: [],
      historyCriterionNumber: null,
      loadingHistory: false,
    };
  },
  mounted() {
    if (this.expanded && this.sar.report_id) {
      this.fetchEvidences(this.expanded);
    }
  },
  watch: {
    'sar.report_id': function(newVal) {
      if (newVal && this.expanded) {
        this.fetchEvidences(this.expanded);
      }
    }
  },
  methods: {
    toggleExpand(num) {
      this.expanded = this.expanded === num ? null : num;
      if (this.expanded && this.sar.report_id) {
        this.fetchEvidences(num);
      }
    },
    async fetchEvidences(criterionNumber) {
      try {
        const res = await axios.get(`/api/v1/nba/workflow/${this.sar.report_id}/criteria/${criterionNumber}/evidence`);
        if (res.data && res.data.success) {
          this.evidences = {
            ...this.evidences,
            [criterionNumber]: res.data.data
          };
        }
      } catch (err) {
        console.error('Failed to fetch evidence:', err);
      }
    },
    async uploadEvidence(event, criterionNumber) {
      const file = event.target.files[0];
      if (!file) return;

      const formData = new FormData();
      formData.append('evidence_file', file);
      if (this.uploadReason[criterionNumber]) {
        formData.append('upload_reason', this.uploadReason[criterionNumber]);
      }

      this.uploading = { ...this.uploading, [criterionNumber]: true };
      
      try {
        const res = await axios.post(`/api/v1/nba/workflow/${this.sar.report_id}/criteria/${criterionNumber}/evidence`, formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
        });
        
        if (res.data && res.data.success) {
          event.target.value = ''; // reset file input
          this.uploadReason = { ...this.uploadReason, [criterionNumber]: '' }; // reset reason field
          this.fetchEvidences(criterionNumber); // reload evidence list
        }
      } catch (err) {
        alert(err.response?.data?.message || 'Failed to upload evidence.');
      } finally {
        this.uploading = { ...this.uploading, [criterionNumber]: false };
      }
    },
    async deleteEvidence(evidenceId, criterionNumber) {
      if (!confirm('Are you sure you want to delete this evidence version?')) return;
      
      try {
        const res = await axios.delete(`/api/v1/nba/evidence/${evidenceId}`);
        if (res.data && res.data.success) {
          this.fetchEvidences(criterionNumber);
        }
      } catch (err) {
        alert('Failed to delete evidence.');
      }
    },
    downloadEvidence(ev) {
      window.open(`/api/v1/nba/evidence/${ev.id}/download`, '_blank');
    },
    async openVersionHistory(criterionNumber) {
      this.historyCriterionNumber = criterionNumber;
      this.showHistoryModal = true;
      this.loadingHistory = true;
      this.historyList = [];

      try {
        const res = await axios.get(`/api/v1/nba/workflow/${this.sar.report_id}/criteria/${criterionNumber}/evidence/history`);
        if (res.data && res.data.success) {
          this.historyList = res.data.data;
        }
      } catch (err) {
        console.error('Failed to load evidence history:', err);
      } finally {
        this.loadingHistory = false;
      }
    }
  },
};
</script>
